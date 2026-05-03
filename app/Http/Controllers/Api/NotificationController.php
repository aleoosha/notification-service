<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateNotificationAction;
use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateNotificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        private readonly CreateNotificationAction $createAction,
        private readonly NotificationRepositoryInterface $repository
    ) {}

    public function store(CreateNotificationRequest $request): JsonResponse
    {

        $notification = $this->createAction->execute($request->toDto());

        return $this->success(
            data: $notification,
            message: 'Notification created and queued for processing',
            code: 201
        );
    }

    public function show(int $id): JsonResponse
    {
        $notification = $this->repository->findById($id);

        if (!$notification) {
            return $this->error('Notification not found', 404);
        }

        return $this->success([
            'id' => $notification->id,
            'status' => $notification->status,
            'channel' => $notification->channel,
            'updated_at' => $notification->updated_at,
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => ['required', 'integer'],
            'status'  => ['nullable', 'string'],
            'channel' => ['nullable', 'string'],
        ]);

        $history = $this->repository->getHistory(
            userId: (int) $request->query('user_id'),
            status: $request->query('status'),
            channel: $request->query('channel')
        );

        return $this->success($history);
    }
}
