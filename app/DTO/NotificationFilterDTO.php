<?php

namespace App\DTO;

readonly class NotificationFilterDTO
{
    public function __construct(
        public int $userId,
        public ?string $status = null,
        public ?string $channel = null,
    ) {}
}
