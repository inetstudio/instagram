<?php

namespace InetStudio\Instagram\Users\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

/**
 * Class UsersBindingsServiceProvider.
 */
class UsersBindingsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
    * @var  array
    */
    public $bindings = [
        'InetStudio\Instagram\Users\Contracts\Repositories\UsersRepositoryContract' => 'InetStudio\Instagram\Users\Repositories\UsersRepository',
        'InetStudio\Instagram\Users\Contracts\Models\UserModelContract' => 'InetStudio\Instagram\Users\Models\UserModel',
        'InetStudio\Instagram\Users\Contracts\Services\Back\UsersServiceContract' => 'InetStudio\Instagram\Users\Services\Back\UsersService',
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
