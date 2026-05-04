<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ReportStatus;
use App\Events\ReportRequested;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid(),
            'user_id' => User::factory(),
            'status' => ReportStatus::PENDING,
            'event_name' => ReportRequested::class,
            'requested_at' => now(),
            'attempts' => 0,
        ];
    }
}
