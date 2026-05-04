<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\NotificationChannel;
use App\Enums\NotificationStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NotificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'idempotency_key' => Str::uuid()->toString(),
            'user_id' => User::factory(),
            'text' => $this->faker->sentence(10),
            'channel' => $this->faker->randomElement(NotificationChannel::cases()),
            'event_name' => 'App\Events\NotificationCreated',
            'status' => $this->faker->randomElement(NotificationStatus::cases()),
            'attempts' => $this->faker->numberBetween(0, 5),
            'last_attempt_at' => $this->faker->optional()->dateTimeBetween('-1 day', 'now'),
        ];
    }
}
