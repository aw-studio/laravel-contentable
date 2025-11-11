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

        $this->registerContentTypes();

    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/contentable.php', 'contentable');
    }

    protected function registerContentTypes(): void
    {
        $registry = $this->app->make(\AwStudio\Contentable\Support\ContentTypeRegistry::class);

        $directories = config('contentable.content_types', []);

        foreach ($directories as $path) {
            foreach (glob($path.'/*.php') as $file) {
                $class = $this->classFromFile($file);

                if (is_subclass_of($class, \AwStudio\Contentable\Contracts\ContentType::class)) {
                    $registry->register($class);
                }
            }
        }
    }

    // resolve FQCN from file
    protected function classFromFile(string $file): string
    {
        $contents = file_get_contents($file);
        preg_match('/namespace (.*?);/', $contents, $ns);
        preg_match('/class (\w+)/', $contents, $cls);

        return $ns[1].'\\'.$cls[1];
    }
}
