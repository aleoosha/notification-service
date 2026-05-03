<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\CreateNotificationAction;
use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateNotificationRequest;
use App\Http\Requests\GetNotificationHistoryRequest;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function __construct(
        private readonly CreateNotificationAction $createAction,
        private readonly NotificationRepositoryInterface $repository
    ) {}

    /**
     * Создание уведомления.
     */
    public function store(CreateNotificationRequest $request): JsonResponse
    {
        $notification = $this->createAction->execute($request->toDto());

        return $this->success(
            data: $notification,
            message: 'Notification created and queued for processing',
            code: 201
        );
    }

    /**
     * Получение статуса уведомления.
     */
    public function show(int $id): JsonResponse
    {
        $notification = $this->repository->findById($id);

        if (! $notification instanceof Notification) {
            return $this->error('Notification not found', 404);
        }

        return $this->success([
            'id' => $notification->id,
            'status' => $notification->status,
            'channel' => $notification->channel,
            'updated_at' => $notification->updated_at,
        ]);
    }

    /**
     * История уведомлений пользователя.
     */
    public function index(GetNotificationHistoryRequest $request): JsonResponse
    {
        $history = $this->repository->getHistory($request->toDto());

        return $this->success($history);
    }
}
