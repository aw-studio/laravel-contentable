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
        foreach (config('contentable.paths', []) as $path) {
            foreach (glob($path.'/*.php') as $file) {
                // convert file path to FQCN
                $class = $this->getClassFullNameFromFile($file);

                if (is_subclass_of($class, \AwStudio\Contentable\Contracts\ContentType::class)) {
                    \AwStudio\Contentable\ContentRegistry::register($class);
                }
            }
        }
    }

    /**
     * Convert a file path to fully-qualified class name.
     */
    protected function getClassFullNameFromFile(string $file): ?string
    {
        $content = file_get_contents($file);

        preg_match('/namespace\s+(.+);/', $content, $nsMatch);
        preg_match('/class\s+(\w+)/', $content, $classMatch);

        if (! isset($nsMatch[1], $classMatch[1])) {
            return null;
        }

        return $nsMatch[1].'\\'.$classMatch[1];
    }
}
