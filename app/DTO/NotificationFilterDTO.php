<?php

declare(strict_types=1);

namespace App\DTO;

readonly class NotificationFilterDTO
{
    public function __construct(
        public int $userId,
        public ?string $status = null,
        public ?string $channel = null,
    ) {}
}
