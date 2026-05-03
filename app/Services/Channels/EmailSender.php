<?php

declare(strict_types=1);

namespace App\Services\Channels;

use App\Contracts\Services\NotificationSenderInterface;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class EmailSender implements NotificationSenderInterface
{
    /**
     * Отправка уведомления по Email.
     */
    public function send(Notification $notification): bool
    {
        // Принудительная ошибка для тестов
        if (str_contains($notification->text, 'force_error')) {
            return false;
        }

        // Принудительный успех для тестов
        if (str_contains($notification->text, 'force_success')) {
            return true;
        }

        // Имитация нестабильного соединения (20% вероятность ошибки)
        if (random_int(1, 100) <= 20) {
            Log::warning('Random failure for Email sending', [
                'user_id' => $notification->user_id,
                'notification_id' => $notification->id,
            ]);

            return false;
        }

        Log::info('Email sent successfully', [
            'user_id' => $notification->user_id,
            'notification_id' => $notification->id,
        ]);

        return true;
    }
}
