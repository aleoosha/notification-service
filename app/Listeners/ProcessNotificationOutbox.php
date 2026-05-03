<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\NotificationCreated;
use App\Jobs\SendNotificationJob;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class ProcessNotificationOutbox implements ShouldHandleEventsAfterCommit
{
    /**
     * Обработать событие создания уведомления.
     */
    public function handle(NotificationCreated $event): void
    {
        SendNotificationJob::dispatch($event->notification);
    }
}
