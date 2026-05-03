<?php

declare(strict_types=1);

namespace App\Services\Channels;

use App\Contracts\Services\NotificationSenderInterface;
use App\Enums\NotificationChannel;
use Illuminate\Support\Manager;

class NotificationManager extends Manager
{
    /**
     * Получить экземпляр драйвера сендера.
     *
     * @param  string|null  $driver
     */
    public function driver($driver = null): NotificationSenderInterface
    {
        // Если драйвер не передан, берем значение 'email' из getDefaultDriver
        $driver = $driver ?: $this->getDefaultDriver();

        /** @var NotificationSenderInterface */
        return parent::driver($driver);
    }

    /**
     * Получить имя драйвера по умолчанию.
     */
    public function getDefaultDriver(): string
    {
        return NotificationChannel::EMAIL->value;
    }

    /**
     * Создать драйвер для отправки Email.
     */
    protected function createEmailDriver(): NotificationSenderInterface
    {
        return new EmailSender;
    }

    /**
     * Создать драйвер для отправки Telegram.
     */
    protected function createTelegramDriver(): NotificationSenderInterface
    {
        return new TelegramSender;
    }
}
