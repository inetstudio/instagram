<?php

namespace InetStudio\Instagram\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class InstagramBindingsServiceProvider.
 */
class InstagramBindingsServiceProvider extends ServiceProvider
{
    /**
    * @var  bool
    */
    protected $defer = true;

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
