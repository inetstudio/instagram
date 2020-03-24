<?php

namespace InetStudio\Instagram\Posts\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

/**
 * Class PostsServiceProvider.
 */
class PostsServiceProvider extends ServiceProvider
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
                'InetStudio\Instagram\Posts\Console\Commands\CreateFoldersCommand',
                'InetStudio\Instagram\Posts\Console\Commands\SetupCommand',
            ]);
        }
    }

    /**
     * Регистрация ресурсов.
     */
    protected function registerPublishes(): void
    {
        $this->publishes([
            __DIR__.'/../../config/instagram_posts.php' => config_path('instagram_posts.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__.'/../../config/filesystems.php', 'filesystems.disks'
        );

        if ($this->app->runningInConsole()) {
            if (! Schema::hasTable('instagram_posts')) {
                $timestamp = date('Y_m_d_His', time());
                $this->publishes([
                    __DIR__.'/../../database/migrations/create_instagram_posts_tables.php.stub' => database_path('migrations/'.$timestamp.'_create_instagram_posts_tables.php'),
                ], 'migrations');
            }
        }
    }
}
