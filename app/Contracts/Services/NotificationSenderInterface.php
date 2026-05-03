<?php

namespace App\Contracts\Services;

use App\Models\Notification;

interface NotificationSenderInterface
{
    public function send(Notification $notification): bool;
}
