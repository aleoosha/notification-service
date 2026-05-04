<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Str;

it('creates a notification via api', function () {
    $user = User::factory()->create();
    $key = Str::uuid()->toString();

    $payload = [
        'user_id' => $user->id,
        'text' => 'Test message',
        'channel' => 'email',
    ];

    $response = $this->postJson('/api/notifications', $payload, [
        'X-Idempotency-Key' => $key,
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.status', 'pending');

    $this->assertDatabaseHas('notifications', [
        'idempotency_key' => $key,
        'user_id' => $user->id,
    ]);
});

it('prevents duplicates with idempotency middleware', function () {
    $user = User::factory()->create();
    $key = 'dup-key';

    $payload = [
        'user_id' => $user->id,
        'text' => 'First version',
        'channel' => 'telegram',
    ];

    $this->postJson('/api/notifications', $payload, ['X-Idempotency-Key' => $key])
        ->assertStatus(201);

    $payload['text'] = 'Second version';
    $response = $this->postJson('/api/notifications', $payload, ['X-Idempotency-Key' => $key]);

    $response->assertStatus(201);
    $response->assertJsonPath('data.text', 'First version');

    $this->assertDatabaseCount('notifications', 1);
});
