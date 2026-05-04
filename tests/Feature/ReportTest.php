<?php

declare(strict_types=1);

use App\Enums\ReportStatus;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

it('starts report generation with valid period and prevents double requests', function () {
    $user = User::factory()->create();

    $payload = [
        'start_date' => now()->subDays(7)->format('Y-m-d'),
        'end_date' => now()->format('Y-m-d'),
    ];

    $this->postJson("/api/reports/{$user->uuid}", $payload)
        ->assertStatus(202)
        ->assertJsonPath('data.status', 'pending');

    $this->postJson("/api/reports/{$user->uuid}", $payload)
        ->assertStatus(409);
});

it('validates report period dates', function () {
    $user = User::factory()->create();

    $this->postJson("/api/reports/{$user->uuid}", [
        'start_date' => '2024-05-01',
        'end_date' => '2024-04-01',
    ])->assertStatus(422);
});

it('downloads completed report file', function () {
    $report = Report::factory()->create([
        'status' => ReportStatus::COMPLETED,
        'file_path' => 'reports/test.csv',
    ]);

    Storage::disk('local')->put('reports/test.csv', 'test content');

    $this->getJson("/api/reports/{$report->uuid}/download")
        ->assertStatus(200)
        ->assertHeader('Content-Type', 'text/csv; charset=utf-8');

    Storage::disk('local')->delete('reports/test.csv');
});
