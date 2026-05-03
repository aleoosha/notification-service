<?php

namespace App\Providers;

use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\Events\NotificationCreated;
use App\Listeners\ProcessNotificationOutbox;
use App\Repositories\EloquentNotificationRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            NotificationRepositoryInterface::class,
            EloquentNotificationRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            NotificationCreated::class,
            ProcessNotificationOutbox::class
        );
    }
}
