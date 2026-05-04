<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface OutboxRepositoryInterface
{
    /**
     * @return Collection<int, Model>
     */
    public function getPending(int $limit = 50): Collection;
}
