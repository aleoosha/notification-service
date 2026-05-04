<?php

declare(strict_types=1);

use App\Models\Notification;
use App\Models\Report;
use App\Models\User;

it('sets user_id to null in related records when user is soft deleted', function () {
    $user = User::factory()->create();

    $notification = Notification::factory()->create(['user_id' => $user->id]);
    $report = Report::factory()->create(['user_id' => $user->id]);

    $user->delete();

    expect($notification->fresh()->user_id)->toBeNull()
        ->and($report->fresh()->user_id)->toBeNull();
});
