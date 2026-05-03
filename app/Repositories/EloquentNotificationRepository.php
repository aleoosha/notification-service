<?php

namespace App\Repositories;

use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\DTO\NotificationFilterDTO;
use App\Enums\NotificationStatus;
use App\Models\Notification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentNotificationRepository implements NotificationRepositoryInterface
{
    public function create(array $data): Notification
    {
        return Notification::create($data);
    }

    public function findById(int $id): ?Notification
    {
        return Notification::find($id);
    }

    public function getHistory(NotificationFilterDTO $filters): LengthAwarePaginator
    {
        return Notification::query()
            ->where('user_id', $filters->userId)
            ->when($filters->status, fn ($q) => $q->where('status', $filters->status))
            ->when($filters->channel, fn ($q) => $q->where('channel', $filters->channel))
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function getPending(int $limit = 50): Collection
    {
        return Notification::where('status', NotificationStatus::PENDING)
            ->where('attempts', '<', 5)
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();
    }
}
