<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Str;

/**
 * @property string $uuid
 */
trait HasUuid
{
    /**
     * Этот метод Laravel вызовет автоматически при создании нового экземпляра модели.
     */
    public function initializeHasUuid(): void
    {
        if (empty($this->uuid)) {
            $this->uuid = (string) Str::uuid();
        }
    }

    /**
     * Позволяет Laravel использовать uuid в маршрутах по умолчанию
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
