<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\Contracts\Repositories\OutboxRepositoryInterface;
use App\Contracts\Repositories\ReportRepositoryInterface;
use App\Enums\NotificationStatus;
use App\Enums\ReportStatus;
use App\Models\Notification;
use App\Models\Report;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class ProcessOutboxRelayCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'app:process-outbox 
                            {--limit=50 : Количество записей каждого типа за проход} 
                            {--sleep=2 : Пауза при отсутствии записей (сек)} 
                            {--once : Запустить один цикл и выйти}';

    /**
     * @var string
     */
    protected $description = 'Universal Transactional Outbox Relay for Notifications and Reports';

    /**
     * Выполнение команды.
     */
    public function handle(): int
    {
        /** @var OutboxRepositoryInterface[] $repositories */
        $repositories = [
            app(NotificationRepositoryInterface::class),
            app(ReportRepositoryInterface::class),
        ];

        if ($this->option('once')) {
            $this->process($repositories);

            return self::SUCCESS;
        }

        $this->info('Outbox Relay started. Monitoring Notifications and Reports...');

        while (true) {
            $hasProcessed = $this->process($repositories);

            if (! $hasProcessed) {
                sleep((int) $this->option('sleep'));
            }

            usleep(500000);
        }
    }

    /**
     * Опрос всех зарегистрированных репозиториев.
     *
     * @param  OutboxRepositoryInterface[]  $repositories
     */
    private function process(array $repositories): bool
    {
        $anyProcessed = false;

        foreach ($repositories as $repository) {
            $records = $repository->getPending((int) $this->option('limit'));

            if ($records->isEmpty()) {
                continue;
            }

            $anyProcessed = true;

            foreach ($records as $record) {
                $this->dispatchRecord($record);
            }
        }

        return $anyProcessed;
    }

    /**
     * Обновление попыток и вызов события.
     */
    private function dispatchRecord(Model $record): void
    {
        try {
            /** @var Notification|Report $record */
            $record->update([
                'last_attempt_at' => now(),
                'attempts' => $record->attempts + 1,
            ]);

            /** @var class-string|null $eventClass */
            $eventClass = $record->event_name;

            if ($eventClass && class_exists($eventClass)) {
                event(new $eventClass($record));
                $this->info('Dispatched ['.class_basename($eventClass)."] for UUID: {$record->uuid}");
            } else {
                throw new \RuntimeException("Event class [{$eventClass}] not found or not specified.");
            }
        } catch (Throwable $e) {
            $this->error("Outbox Error (UUID: {$record->uuid}): {$e->getMessage()}");

            if ($record->attempts >= 5) {
                $this->markAsFailed($record);
            }
        }
    }

    /**
     * Установка финального статуса ошибки.
     */
    private function markAsFailed(Model $record): void
    {
        $status = match (true) {
            $record instanceof Report => ReportStatus::FAILED,
            default => NotificationStatus::ERROR,
        };

        $record->update(['status' => $status]);
    }
}
