<?php

namespace App\Services\Channels;

use App\Contracts\Services\NotificationSenderInterface;
use App\Enums\NotificationChannel;
use Illuminate\Support\Manager;
use InvalidArgumentException;

class NotificationManager extends Manager
{
    public function driver($driver = null): NotificationSenderInterface
    {
        $driver = $driver ?: $this->getDefaultDriver();

        /** @var NotificationSenderInterface */
        return parent::driver($driver);
    }

    public function getDefaultDriver(): string
    {
        return NotificationChannel::EMAIL->value;
    }

    protected function createEmailDriver(): NotificationSenderInterface
    {
        return new EmailSender;
    }

    protected function createTelegramDriver(): NotificationSenderInterface
    {
        return new TelegramSender;
    }
}
