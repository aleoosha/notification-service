<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\Models\Notification;

interface NotificationSenderInterface
{
    /**
     * Отправка уведомления через конкретный канал.
     */
    public function send(Notification $notification): bool;
}
