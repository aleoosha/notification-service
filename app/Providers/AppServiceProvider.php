<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\Contracts\Repositories\ReportRepositoryInterface;
use App\Events\NotificationCreated;
use App\Listeners\ProcessNotificationOutbox;
use App\Models\User;
use App\Observers\UserObserver;
use App\Repositories\EloquentNotificationRepository;
use App\Repositories\EloquentReportRepository;
use App\Services\Channels\NotificationManager;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Привязка репозитория к контракту
        $this->app->bind(
            NotificationRepositoryInterface::class,
            EloquentNotificationRepository::class
        );

        // Регистрация менеджера уведомлений как синглтона
        $this->app->singleton(NotificationManager::class, function ($app) {
            return new NotificationManager($app);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Регистрация слушателя для Outbox паттерна
        Event::listen(
            NotificationCreated::class,
            ProcessNotificationOutbox::class
        );

        User::observe(UserObserver::class);

        $this->app->bind(NotificationRepositoryInterface::class, EloquentNotificationRepository::class);
        $this->app->bind(ReportRepositoryInterface::class, EloquentReportRepository::class);

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
