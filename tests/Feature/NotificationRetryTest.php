<?php

use App\Enums\NotificationChannel;
use App\Enums\NotificationStatus;
use App\Jobs\SendNotificationJob;
use App\Models\Notification;

it('retries notification sending on failure', function () {
    $notification = Notification::factory()->create([
        'text' => 'force_error',
        'status' => NotificationStatus::PENDING,
        'channel' => NotificationChannel::EMAIL,
    ]);

    $job = new SendNotificationJob($notification);

    try {
        app()->call([$job, 'handle']);
    } catch (Exception $e) {
    }

    $notification->refresh();
    expect($notification->status)->toBe(NotificationStatus::PENDING);

    expect($notification->status)->not->toBe(NotificationStatus::ERROR);
});
