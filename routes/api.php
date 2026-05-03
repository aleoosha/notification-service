<?php

use App\Http\Controllers\Api\NotificationController;
use Illuminate\Support\Facades\Route;

/*

|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('notifications')->group(function () {

    // Создание уведомления (защищено идемпотентностью через заголовок X-Idempotency-Key)
    Route::post('/', [NotificationController::class, 'store']);

    // Получение статуса конкретного уведомления
    Route::get('/{id}', [NotificationController::class, 'show'])
        ->whereNumber('id');

    // История уведомлений пользователя с фильтрацией (user_id передаем в query)
    Route::get('/', [NotificationController::class, 'index']);

});

// Роут для доп. задания (отчеты) — добавим позже
// Route::prefix('reports')->group(function () { ... });
