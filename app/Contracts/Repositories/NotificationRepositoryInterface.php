<?php

namespace App\Contracts\Repositories;

use App\DTO\NotificationFilterDTO;
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
    public function getHistory(NotificationFilterDTO $filters): LengthAwarePaginator;

    /**
     * Получение списка необработанных уведомлений для фонового процесса.
     *
     * @return Collection<int, Notification>
     */
    public function getPending(int $limit = 50): Collection;
}
