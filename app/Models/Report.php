<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ReportStatus;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $uuid
 * @property int|null $user_id
 * @property ReportStatus $status
 * @property string|null $file_path
 * @property string $event_name
 * @property int $attempts
 * @property Carbon|null $last_attempt_at
 * @property Carbon $requested_at
 * @property Carbon|null $completed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Report extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'status',
        'file_path',
        'event_name',
        'attempts',
        'last_attempt_at',
        'requested_at',
        'completed_at',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'status' => ReportStatus::class,
            'requested_at' => 'datetime',
            'completed_at' => 'datetime',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'last_attempt_at' => 'datetime',
            'attempts' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
