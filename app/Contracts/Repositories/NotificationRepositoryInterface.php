<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\DTO\NotificationFilterDTO;
use App\Models\Notification;
use Illuminate\Pagination\LengthAwarePaginator;

interface NotificationRepositoryInterface
{
    /**
     * Создание новой записи уведомления.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Notification;

    /**
     * Поиск уведомления по ID.
     */
    public function findById(int $id): ?Notification;

    /**
     * Поиск уведомления по ID.
     */
    public function findByUuid(string $uuid): ?Notification;

    /**
     * Получение истории уведомлений пользователя с пагинацией и фильтрами.
     *
     * @return LengthAwarePaginator<Notification>
     */
    public function getHistory(NotificationFilterDTO $filters): LengthAwarePaginator;
}
