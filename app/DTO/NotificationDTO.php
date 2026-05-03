<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enums\NotificationChannel;
use App\Events\NotificationCreated;

readonly class NotificationDTO
{
    public function __construct(
        public string $idempotencyKey,
        public int $userId,
        public string $text,
        public NotificationChannel $channel,
        public string $eventName = NotificationCreated::class,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            idempotencyKey: (string) $data['idempotency_key'],
            userId: (int) $data['user_id'],
            text: (string) $data['text'],
            channel: $data['channel'] instanceof NotificationChannel
                ? $data['channel']
                : NotificationChannel::from($data['channel']),
        );
    }
}
