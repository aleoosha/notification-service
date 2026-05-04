<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\DTO\NotificationFilterDTO;
use App\Enums\NotificationStatus;
use App\Models\Notification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentNotificationRepository implements NotificationRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Notification
    {
        return Notification::create($data);
    }

    public function findById(int $id): ?Notification
    {
        $notification = Notification::find($id);

        return $notification instanceof Notification ? $notification : null;
    }

    /**
     * @return LengthAwarePaginator<Notification>
     */
    public function getHistory(NotificationFilterDTO $filters): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator<Notification> $result */
        $result = Notification::query()
            ->where('user_id', $filters->userId)
            ->when($filters->status, fn ($q) => $q->where('status', $filters->status))
            ->when($filters->channel, fn ($q) => $q->where('channel', $filters->channel))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return $result;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getPending(int $limit = 50): Collection
    {
        return DB::transaction(function () use ($limit) {
            /** @var Collection<int, Notification> */
            return Notification::query()
                ->where('status', NotificationStatus::PENDING)
                ->where('attempts', '<', 5)
                ->orderBy('created_at', 'asc')
                ->limit($limit)
                ->lock('FOR UPDATE SKIP LOCKED')
                ->get();
        });
    }
}
