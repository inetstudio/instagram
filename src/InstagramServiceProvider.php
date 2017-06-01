<?php

namespace InetStudio\Instagram;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class InstagramServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../database/' => base_path('database'),
        ], 'database');

        $this->publishes([
            __DIR__.'/../config/instagram.php' => config_path('instagram.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__.'/../config/filesystems.php', 'filesystems.disks'
        );
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton('InstagramID', function () {
            return new InstagramID();
        });

        $this->app->singleton('InstagramUser', function () {
            return new InstagramUser();
        });

        $this->app->singleton('InstagramPost', function () {
            return new InstagramPost();
        });

        $loader = AliasLoader::getInstance();
        $loader->alias('InstagramID', 'InetStudio\Instagram\Facades\InstagramIDFacade');
        $loader->alias('InstagramPost', 'InetStudio\Instagram\Facades\InstagramPostFacade');
        $loader->alias('InstagramUser', 'InetStudio\Instagram\Facades\InstagramUserFacade');

        $this->app->register('Spatie\MediaLibrary\MediaLibraryServiceProvider');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'InstagramID',
            'InstagramPost',
            'InstagramUser',
        ];
    }
}
