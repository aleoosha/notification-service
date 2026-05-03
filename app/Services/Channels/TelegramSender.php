<?php

namespace App\Services\Channels;

use App\Contracts\Services\NotificationSenderInterface;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class TelegramSender implements NotificationSenderInterface
{
    public function send(Notification $notification): bool
    {
        if (str_contains($notification->text, 'force_error')) {
            return false;
        }

        if (str_contains($notification->text, 'force_success')) {
            return true;
        }

        if (rand(1, 100) <= 20) {
            Log::warning("Random failure for Telegram to user: {$notification->user_id}");

            return false;
        }

        Log::info("Telegram message sent to user {$notification->user_id}");

        return true;
    }
}
