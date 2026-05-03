<?php

namespace App\DTO;

use App\Enums\NotificationChannel;

readonly class NotificationDTO
{
    public function __construct(
        public string $idempotencyKey,
        public int $userId,
        public string $text,
        public NotificationChannel $channel,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            idempotencyKey: $data['idempotency_key'],
            userId: (int) $data['user_id'],
            text: $data['text'],
            channel: NotificationChannel::from($data['channel']),
        );
    }
}
