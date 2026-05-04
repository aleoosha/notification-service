<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\ReportRepositoryInterface;
use App\DTO\ReportOrderDTO;
use App\Enums\ReportStatus;
use App\Events\ReportRequested;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class EloquentReportRepository implements ReportRepositoryInterface
{
    public function findByUuid(string $uuid): ?Report
    {
        return Report::where('uuid', $uuid)->first();
    }

    public function createForUser(User $user, ReportOrderDTO $dto): Report
    {
        return DB::transaction(function () use ($user, $dto) {
            $exists = Report::where('user_id', $user->id)
                ->where('status', ReportStatus::PENDING)
                ->exists();

            if ($exists) {
                throw new ConflictHttpException('Report is already being generated.');
            }

            return Report::create([
                'user_id' => $user->id,
                'status' => ReportStatus::PENDING,
                'start_date' => $dto->startDate,
                'end_date' => $dto->endDate,
                'event_name' => ReportRequested::class,
                'requested_at' => now(),
                'attempts' => 0,
            ]);
        });
    }

    /** @return Collection<int, Model> */
    public function getPending(int $limit = 50): Collection
    {
        return DB::transaction(function () use ($limit) {
            /** @var Collection<int, Model> $records */
            $records = Report::query()
                ->where('status', ReportStatus::PENDING)
                ->where('attempts', '<', 5)
                ->orderBy('created_at', 'asc')
                ->limit($limit)
                ->lock('FOR UPDATE SKIP LOCKED')
                ->get()
                ->toBase(); // Превращает Eloquent Collection в Support Collection

            return $records;
        });
    }
}
