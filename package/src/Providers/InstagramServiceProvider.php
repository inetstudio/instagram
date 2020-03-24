<?php

namespace InetStudio\Instagram\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class InstagramServiceProvider.
 */
class InstagramServiceProvider extends ServiceProvider
{
    /**
     * Загрузка сервиса.
     */
    public function boot()
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
                'InetStudio\Instagram\Console\Commands\SetupCommand',
            ]);
        }
    }

    /**
     * Регистрация ресурсов.
     */
    protected function registerPublishes(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/services.php', 'services'
        );
    }
}
