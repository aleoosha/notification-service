<?php

namespace App\Actions;

use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\DTO\NotificationDTO;
use App\Enums\NotificationStatus;
use App\Events\NotificationCreated;
use App\Models\Notification;
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class CreateNotificationAction
{
    public function __construct(
        private readonly NotificationRepositoryInterface $repository,
        private readonly CacheFactory $cache
    ) {}

    public function execute(NotificationDTO $dto): Notification
    {
        $repository = $this->cache->store();
        $store = $repository->getStore();

        if (! $store instanceof LockProvider) {
            throw new \RuntimeException('Current cache store does not support atomic locks. Driver: '.config('cache.default'));
        }

        $lock = $repository->lock(
            'create_notification_'.$dto->idempotencyKey,
            10
        );

        $result = $lock->get(function () use ($dto) {
            return DB::transaction(function () use ($dto) {
                $existing = Notification::where('idempotency_key', $dto->idempotencyKey)->first();

                if ($existing) {
                    return $existing;
                }

                return $this->repository->create([
                    'idempotency_key' => $dto->idempotencyKey,
                    'user_id' => $dto->userId,
                    'text' => $dto->text,
                    'channel' => $dto->channel,
                    'event_name' => NotificationCreated::class,
                    'status' => NotificationStatus::PENDING,
                ]);
            });
        });

        if ($result === false) {
            throw new ConflictHttpException('Request is already being processed.');
        }

        return $result;
    }
}
