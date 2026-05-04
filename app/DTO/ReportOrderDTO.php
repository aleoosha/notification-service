<?php

declare(strict_types=1);

namespace App\DTO;

use Carbon\Carbon;

readonly class ReportOrderDTO
{
    public function __construct(
        public Carbon $startDate,
        public Carbon $endDate,
    ) {}
}
