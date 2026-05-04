<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\NotificationStatus;
use App\Events\NotificationCreated;
use App\Models\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;

it('dispatches events via outbox command with once flag', function () {
    Event::fake();

    Notification::factory()->count(3)->create([
        'status' => NotificationStatus::PENDING,
        'event_name' => NotificationCreated::class,
    ]);

    Artisan::call('notifications:process-outbox', ['--once' => true]);

    Event::assertDispatched(NotificationCreated::class, 3);
});
