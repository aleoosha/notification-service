<?php

namespace App\Models;

use App\Enums\NotificationChannel;
use App\Enums\NotificationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'idempotency_key',
        'user_id',
        'text',
        'channel',
        'status',
        'event_name',
        'attempts',
        'last_attempt_at',
    ];

    protected function casts(): array
    {
        return [
            'channel' => NotificationChannel::class,
            'status' => NotificationStatus::class,
            'last_attempt_at' => 'datetime',
            'attempts' => 'integer',
        ];
    }
}
