<?php

namespace App\Contracts\Repositories;

use App\Models\Notification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface NotificationRepositoryInterface
{
    /**
     * Создание новой записи уведомления.
     */
    public function create(array $data): Notification;

    /**
     * Поиск уведомления по ID.
     */
    public function findById(int $id): ?Notification;

    /**
     * Получение истории уведомлений пользователя с пагинацией и фильтрами.
     */
    public function getHistory(int $userId, ?string $status, ?string $channel): LengthAwarePaginator;

    /**
     * Получение списка необработанных уведомлений для фонового процесса.
     * 
     * @param int $limit
     * @return Collection<int, Notification>
     */
    public function getPending(int $limit = 50): Collection;
}
