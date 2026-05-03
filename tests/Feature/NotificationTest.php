<?php

it('creates a notification via api', function () {
    $this->withoutExceptionHandling();

    $payload = [
        'idempotency_key' => 'unique-123',
        'user_id' => 1,
        'text' => 'Test message',
        'channel' => 'email',
    ];

    $response = $this->postJson('/api/notifications', $payload, [
        'X-Idempotency-Key' => 'unique-123',
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.status', 'pending');

    $this->assertDatabaseHas('notifications', [
        'idempotency_key' => 'unique-123',
        'status' => 'pending',
    ]);
});

it('prevents duplicates with idempotency middleware', function () {
    $payload = [
        'idempotency_key' => 'dup-key',
        'user_id' => 1,
        'text' => 'First version',
        'channel' => 'telegram',
    ];

    $this->postJson('/api/notifications', $payload, ['X-Idempotency-Key' => 'dup-key']);

    $payload['text'] = 'Second version';
    $response = $this->postJson('/api/notifications', $payload, ['X-Idempotency-Key' => 'dup-key']);

    $response->assertStatus(201);
    $response->assertJsonPath('data.text', 'First version');
    $this->assertDatabaseCount('notifications', 1);
});
