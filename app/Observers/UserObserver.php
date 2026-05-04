<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Notification;
use App\Models\Report;
use App\Models\User;

class UserObserver
{
    /**
     * Вызывается при удалении пользователя (включая мягкое).
     */
    public function deleted(User $user): void
    {
        Notification::where('user_id', $user->id)->update(['user_id' => null]);
        Report::where('user_id', $user->id)->update(['user_id' => null]);
    }
}
