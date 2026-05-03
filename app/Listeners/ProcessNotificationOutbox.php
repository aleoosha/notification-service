<?php

namespace App\Listeners;

use App\Events\NotificationCreated;
use App\Jobs\SendNotificationJob;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class ProcessNotificationOutbox implements ShouldHandleEventsAfterCommit
{
    public function handle(NotificationCreated $event): void
    {
        SendNotificationJob::dispatch($event->notification);
    }
}
