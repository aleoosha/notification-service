<?php

use App\Services\Channels\EmailSender;
use App\Services\Channels\NotificationManager;
use App\Services\Channels\TelegramSender;

it('resolves email driver correctly', function () {
    $manager = app(NotificationManager::class);

    expect($manager->driver('email'))->toBeInstanceOf(EmailSender::class);
});

it('resolves telegram driver correctly', function () {
    $manager = app(NotificationManager::class);

    expect($manager->driver('telegram'))->toBeInstanceOf(TelegramSender::class);
});

it('throws exception for unknown driver', function () {
    $manager = app(NotificationManager::class);

    $manager->driver('whatsapp');
})->throws(InvalidArgumentException::class);
