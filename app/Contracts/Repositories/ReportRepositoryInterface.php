<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\DTO\ReportOrderDTO;
use App\Models\Report;
use App\Models\User;

interface ReportRepositoryInterface extends OutboxRepositoryInterface
{
    public function findByUuid(string $uuid): ?Report;

    /**
     * Атомарное создание записи отчета через DTO.
     */
    public function createForUser(User $user, ReportOrderDTO $dto): Report;
}
