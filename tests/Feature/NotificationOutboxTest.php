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

    Notification::query()->delete();

    Notification::factory()->count(3)->create([
        'status' => NotificationStatus::PENDING,
        'attempts' => 0,
        'event_name' => NotificationCreated::class,
    ]);

    Artisan::call('app:process-outbox', [
        '--once' => true,
        '--limit' => 10,
    ]);

    Event::assertDispatched(NotificationCreated::class, 3);
});
