<?php

namespace InetStudio\Instagram\Providers;

use Illuminate\Support\ServiceProvider;
use InetStudio\Instagram\Console\Commands\SetupCommand;
use InetStudio\Instagram\Console\Commands\CreateFoldersCommand;
use InetStudio\Instagram\Services\Back\InstagramIDService;
use InetStudio\Instagram\Services\Back\InstagramPostsService;
use InetStudio\Instagram\Services\Back\InstagramUsersService;
use InetStudio\Instagram\Contracts\Services\Back\InstagramIDServiceContract;
use InetStudio\Instagram\Contracts\Services\Back\InstagramPostsServiceContract;
use InetStudio\Instagram\Contracts\Services\Back\InstagramUsersServiceContract;

/**
 * Class InstagramServiceProvider
 * @package InetStudio\Instagram\Providers
 */
class InstagramServiceProvider extends ServiceProvider
{
    /**
     * Загрузка сервиса.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConsoleCommands();
        $this->registerPublishes();
    }

    /**
     * Регистрация привязки в контейнере.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerBindings();
    }

    /**
     * Регистрация команд.
     *
     * @return void
     */
    protected function registerConsoleCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SetupCommand::class,
                CreateFoldersCommand::class,
            ]);
        }
    }

    /**
     * Регистрация ресурсов.
     *
     * @return void
     */
    protected function registerPublishes(): void
    {
        $this->publishes([
            __DIR__.'/../../config/instagram.php' => config_path('instagram.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__.'/../../config/filesystems.php', 'filesystems.disks'
        );

        if ($this->app->runningInConsole()) {
            if (! class_exists('CreateInstagramTables')) {
                $timestamp = date('Y_m_d_His', time());
                $this->publishes([
                    __DIR__.'/../../database/migrations/create_instagram_tables.php.stub' => database_path('migrations/'.$timestamp.'_create_instagram_tables.php'),
                ], 'migrations');
            }
        }
    }

    /**
     * Регистрация привязок, алиасов и сторонних провайдеров сервисов.
     *
     * @return void
     */
    protected function registerBindings(): void
    {
        $this->app->bind(InstagramIDServiceContract::class, InstagramIDService::class);
        $this->app->bind(InstagramPostsServiceContract::class, InstagramPostsService::class);
        $this->app->bind(InstagramUsersServiceContract::class, InstagramUsersService::class);
    }
}
