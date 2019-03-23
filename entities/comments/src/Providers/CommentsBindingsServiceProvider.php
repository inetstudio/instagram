<?php

namespace InetStudio\Instagram\Comments\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

/**
 * Class CommentsBindingsServiceProvider.
 */
class CommentsBindingsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
    * @var array
    */
    public $bindings = [
        'InetStudio\Instagram\Comments\Contracts\Repositories\CommentsRepositoryContract' => 'InetStudio\Instagram\Comments\Repositories\CommentsRepository',
        'InetStudio\Instagram\Comments\Contracts\Models\CommentModelContract' => 'InetStudio\Instagram\Comments\Models\CommentModel',
        'InetStudio\Instagram\Comments\Contracts\Services\Back\CommentsServiceContract' => 'InetStudio\Instagram\Comments\Services\Back\CommentsService',
    ];

    /**
     * Получить сервисы от провайдера.
     *
     * @return array
     */
    public function provides()
    {
        return array_keys($this->bindings);
    }
}
