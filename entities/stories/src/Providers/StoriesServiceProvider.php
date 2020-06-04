<?php

namespace InetStudio\Instagram\Stories\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

/**
 * Class StoriesServiceProvider.
 */
class StoriesServiceProvider extends ServiceProvider
{
    /**
     * Загрузка сервиса.
     */
    public function boot(): void
    {
        $this->registerConsoleCommands();
        $this->registerPublishes();
    }

    /**
     * Регистрация команд.
     */
    protected function registerConsoleCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                'InetStudio\Instagram\Stories\Console\Commands\CreateFoldersCommand',
                'InetStudio\Instagram\Stories\Console\Commands\SetupCommand',
            ]);
        }
    }

    /**
     * Регистрация ресурсов.
     */
    protected function registerPublishes(): void
    {
        $this->publishes([
            __DIR__.'/../../config/instagram_stories.php' => config_path('instagram_stories.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__.'/../../config/filesystems.php', 'filesystems.disks'
        );

        if ($this->app->runningInConsole()) {
            if (! Schema::hasTable('instagram_stories')) {
                $timestamp = date('Y_m_d_His', time());
                $this->publishes([
                    __DIR__.'/../../database/migrations/create_instagram_stories_tables.php.stub' => database_path('migrations/'.$timestamp.'_create_instagram_stories_tables.php'),
                ], 'migrations');
            }
        }
    }
}
