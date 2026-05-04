<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NotificationChannel;
use App\Enums\NotificationStatus;
use Database\Factories\NotificationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $idempotency_key
 * @property int $user_id
 * @property string $text
 * @property NotificationChannel $channel
 * @property string $event_name
 * @property NotificationStatus $status
 * @property int $attempts
 * @property Carbon|null $last_attempt_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Notification extends Model
{
    /** @use HasFactory<NotificationFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
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

    /**
     * @return array<string, string>
     */
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
