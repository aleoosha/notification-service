<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\CreateNotificationAction;
use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateNotificationRequest;
use App\Http\Requests\GetNotificationHistoryRequest;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
            data: new NotificationResource($notification),
            message: 'Notification created and queued for processing',
            code: 201
        );
    }

    /**
     * Получение статуса конкретного уведомления по UUID.
     */
    public function show(Notification $notification): JsonResponse
    {
        return $this->success(new NotificationResource($notification));
    }

    /**
     * История уведомлений пользователя.
     */
    public function index(GetNotificationHistoryRequest $request): AnonymousResourceCollection
    {
        $history = $this->repository->getHistory($request->toDto());

        return NotificationResource::collection($history);
    }
}
