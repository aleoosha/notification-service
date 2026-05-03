<?php

namespace App\Enums;

enum NotificationChannel: string
{
    case EMAIL = 'email';
    case TELEGRAM = 'telegram';
}
