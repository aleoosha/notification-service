<?php

namespace App\Jobs;

use App\Enums\NotificationChannel;
use App\Enums\NotificationStatus;
use App\Models\Notification;
use App\Services\Channels\NotificationManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Количество попыток выполнения (Гарантия доставки).
     */
    public int $tries = 5;

    /**
     * Задержка между попытками (в секундах).
     */
    public array $backoff = [5, 10, 20, 40, 80];

    public function __construct(
        private readonly Notification $notification
    ) {}

    /**
     * Выполнение задачи.
     */
    public function handle(NotificationManager $manager): void
    {
        /** @var LockProvider $cache */
        $cache = Cache::store();

        $lock = $cache->lock('sending_notification_'.$this->notification->id, 30);

        $lock->get(function () use ($manager) {
            $this->notification->refresh();

            /** @var NotificationStatus $status */
            $status = $this->notification->status;

            if ($status === NotificationStatus::SENT) {
                return;
            }

            try {
                $this->notification->update(['status' => NotificationStatus::PENDING]);

                /** @var NotificationChannel $channel */
                $channel = $this->notification->channel;

                $sender = $manager->driver($channel->value);

                if ($sender->send($this->notification)) {
                    $this->notification->update(['status' => NotificationStatus::SENT]);
                } else {
                    throw new \Exception('The driver failed to send the message.');
                }
            } catch (Throwable $e) {
                Log::error('Notification Job Failed: '.$e->getMessage(), [
                    'id' => $this->notification->id,
                    'attempt' => $this->attempts(),
                ]);

                if ($this->attempts() >= $this->tries) {
                    $this->notification->update(['status' => NotificationStatus::ERROR]);
                }

                throw $e;
            }
        });
    }
}
