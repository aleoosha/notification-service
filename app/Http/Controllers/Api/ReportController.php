<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Contracts\Repositories\ReportRepositoryInterface;
use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderReportRequest;
use App\Http\Resources\ReportResource;
use App\Models\Report;
use App\Models\User;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportRepositoryInterface $repository,
        private readonly FilesystemFactory $storage
    ) {}

    /**
     * Запрос на генерацию отчета.
     */
    public function store(OrderReportRequest $request, User $user): JsonResponse
    {
        $lockKey = "report_generation_{$user->id}";
        $lock = Cache::lock($lockKey, 60);

        $report = $lock->get(function () use ($user, $request) {
            // Передаем DTO в репозиторий
            return $this->repository->createForUser($user, $request->toDto());
        });

        if (! $report) {
            throw new ConflictHttpException('Another request is already in progress.');
        }

        return $this->success(
            data: new ReportResource($report),
            message: 'Report generation started',
            code: 202
        );
    }

    /**
     * Получение статуса отчета.
     */
    public function show(Report $report): JsonResponse
    {
        return $this->success(new ReportResource($report));
    }

    /**
     * Скачивание готового отчета.
     */
    public function download(Report $report): StreamedResponse|JsonResponse
    {
        if ($report->status !== ReportStatus::COMPLETED || ! $report->file_path) {
            return $this->error('Report is not ready or failed', 400);
        }

        $disk = $this->storage->disk('local');

        if (! $disk->exists($report->file_path)) {
            return $this->error('File not found on storage', 404);
        }

        /** @var StreamedResponse $response */
        $response = $disk->download(
            $report->file_path,
            "report_{$report->uuid}.csv"
        );

        return $response;
    }
}
