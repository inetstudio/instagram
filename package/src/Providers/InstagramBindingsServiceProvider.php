<?php

namespace InetStudio\Instagram\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

/**
 * Class InstagramBindingsServiceProvider.
 */
class InstagramBindingsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
    * @var  array
    */
    public $bindings = [
        'InetStudio\Instagram\Contracts\Services\Back\InstagramServiceContract' => 'InetStudio\Instagram\Services\Back\InstagramService',
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
