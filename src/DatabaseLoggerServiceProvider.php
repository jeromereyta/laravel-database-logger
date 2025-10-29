<?php

declare(strict_types=1);

namespace Jeromereyta\DatabaseLogger;

use Illuminate\Support\ServiceProvider;
use MarkJerome\DatabaseLogger\Console\InstallLoggerCommand;

class DatabaseLoggerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/database-logger.php', 'database-logger');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/database-logger.php' => config_path('database-logger.php'),
        ], 'config');

        $this->commands([
            InstallLoggerCommand::class,
        ]);
    }
}