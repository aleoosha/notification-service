<?php

declare(strict_types=1);

namespace App\Enums;

enum NotificationChannel: string
{
    case EMAIL = 'email';
    case TELEGRAM = 'telegram';
}
