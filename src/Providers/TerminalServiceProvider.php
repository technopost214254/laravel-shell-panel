<?php

namespace Webkul\Terminal\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class TerminalServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        Route::middleware('web')->group(__DIR__.'/../Routes/web.php');

        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'terminal');
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerConfig();
    }

    /**
     * Register package config.
     */
    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/menu.php',
            'menu.admin'
        );
    }
}
