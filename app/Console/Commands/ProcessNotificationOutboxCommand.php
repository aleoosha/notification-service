<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\Enums\NotificationStatus;
use Illuminate\Console\Command;
use Throwable;

class ProcessNotificationOutboxCommand extends Command
{
    /** @var string */
    protected $signature = 'notifications:process-outbox 
                            {--limit=50 : Количество записей за раз} 
                            {--sleep=2 : Пауза между проверками в секундах} 
                            {--once : Запустить один раз и выйти}';

    /** @var string */
    protected $description = 'Scan notifications table and dispatch stored events (Outbox Pattern Relay)';

    public function handle(NotificationRepositoryInterface $repository): int
    {
        if ($this->option('once')) {
            $this->processBatch($repository);
            return self::SUCCESS;
        }

        $this->info('Outbox Relay started. Watching for pending notifications...');

        while (true) {
            $hasProcessed = $this->processBatch($repository);

            if (! $hasProcessed) {
                sleep((int) $this->option('sleep'));
            }
            
            usleep(500000);
        }
    }

    /**
     * Обработка одной пачки уведомлений.
     */
    private function processBatch(NotificationRepositoryInterface $repository): bool
    {
        $notifications = $repository->getPending((int) $this->option('limit'));

        if ($notifications->isEmpty()) {
            return false;
        }

        foreach ($notifications as $notification) {
            try {
                $notification->update([
                    'last_attempt_at' => now(),
                    'attempts' => $notification->attempts + 1,
                ]);

                /** @var class-string $eventClass */
                $eventClass = $notification->event_name;

                if (class_exists($eventClass)) {
                    event(new $eventClass($notification));
                    $this->info("Dispatched event for notification ID: {$notification->id}");
                } else {
                    throw new \RuntimeException("Event class [{$eventClass}] not found.");
                }
            } catch (Throwable $e) {
                $this->error("Failed to process notification {$notification->id}: {$e->getMessage()}");

                if ($notification->attempts >= 5) {
                    $notification->update(['status' => NotificationStatus::ERROR]);
                }
            }
        }

        return true;
    }
}
