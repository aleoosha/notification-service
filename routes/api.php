<?php

declare(strict_types=1);

use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Support\Facades\Route;

/*

|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::middleware('throttle:api')->group(function () {
    Route::prefix('notifications')->group(function () {

        Route::post('/', [NotificationController::class, 'store']);

        Route::get('/{uuid}', [NotificationController::class, 'show'])
            ->whereUuid('uuid');

        Route::get('/', [NotificationController::class, 'index']);

    });

    Route::prefix('reports')->group(function () {
        Route::post('/{user}', [ReportController::class, 'store'])
            ->whereUuid('user');

        Route::get('/{report}', [ReportController::class, 'show'])
            ->whereUuid('report');

        Route::get('/{report}/download', [ReportController::class, 'download'])
            ->whereUuid('report')
            ->name('reports.download');
    });
});
