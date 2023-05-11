<?php

namespace Alexvexone\LaravelOperSms\Providers;

use Alexvexone\LaravelOperSms\Channels\OperSmsChannel;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class OperSmsServiceProvider extends ServiceProvider
{
    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        'opersms' => OperSmsChannel::class,
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        Notification::resolved(function (ChannelManager $service) {
            $service->extend('opersms', function ($app) {
                return $app->make('opersms');
            });
        });
    }

    /**
     * Bootstrap services
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/opersms.php' => config_path('opersms.php'),
            ]);
        }
    }
}
