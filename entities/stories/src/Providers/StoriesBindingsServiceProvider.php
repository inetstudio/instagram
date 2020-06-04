<?php

namespace InetStudio\Instagram\Stories\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

/**
 * Class StoriesBindingsServiceProvider.
 */
class StoriesBindingsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
    * @var  array
    */
    public $bindings = [
        'InetStudio\Instagram\Stories\Contracts\Repositories\StoriesRepositoryContract' => 'InetStudio\Instagram\Stories\Repositories\StoriesRepository',
        'InetStudio\Instagram\Stories\Contracts\Models\StoryModelContract' => 'InetStudio\Instagram\Stories\Models\StoryModel',
        'InetStudio\Instagram\Stories\Contracts\Services\Back\StoriesServiceContract' => 'InetStudio\Instagram\Stories\Services\Back\StoriesService',
    ];

    /**
     * Получить сервисы от провайдера.
     *
     * @return  array
     */
    public function provides()
    {
        return array_keys($this->bindings);
    }
}
