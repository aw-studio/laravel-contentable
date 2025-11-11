<?php

namespace AwStudio\Contentable;

use Illuminate\Support\ServiceProvider;

class ContentableServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/Http/routes.php');

        // Publish config
        $this->publishes([
            __DIR__.'/config/contentable.php' => config_path('contentable.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/contentable.php', 'contentable');
    }
}
