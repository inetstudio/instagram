<?php

namespace InetStudio\Instagram\Comments\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

/**
 * Class CommentsServiceProvider.
 */
class CommentsServiceProvider extends ServiceProvider
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
                'InetStudio\Instagram\Comments\Console\Commands\SetupCommand',
            ]);
        }
    }

    /**
     * Регистрация ресурсов.

     */
    protected function registerPublishes(): void
    {
        if ($this->app->runningInConsole()) {
            if (! Schema::hasTable('instagram_comments')) {
                $timestamp = date('Y_m_d_His', time());
                $this->publishes([
                    __DIR__.'/../../database/migrations/create_instagram_comments_tables.php.stub' => database_path('migrations/'.$timestamp.'_create_instagram_comments_tables.php'),
                ], 'migrations');
            }
        }
    }
}
