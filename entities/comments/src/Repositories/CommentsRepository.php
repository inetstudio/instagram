<?php

namespace InetStudio\Instagram\Comments\Repositories;

use InetStudio\AdminPanel\Repositories\BaseRepository;
use InetStudio\Instagram\Comments\Contracts\Models\CommentModelContract;
use InetStudio\Instagram\Comments\Contracts\Repositories\CommentsRepositoryContract;

/**
 * Class CommentsRepository.
 */
class CommentsRepository extends BaseRepository implements CommentsRepositoryContract
{
    /**
     * CommentsRepository constructor.
     *
     * @param CommentModelContract $model
     */
    public function __construct(CommentModelContract $model)
    {
        $this->model = $model;

        $this->defaultColumns = ['id', 'pk', 'post_pk', 'user_pk', 'additional_info'];
        $this->relations = [
            'user' => function ($query) {
                $query->select(['id', 'pk', 'additional_info']);
            },
            'post' => function ($query) {
                $query->select(['id', 'pk', 'user_pk', 'additional_info']);
            },
        ];
    }

    /**
     * Возвращаем объект по pk, либо создаем новый.
     *
     * @param string $pk
     *
     * @return mixed
     */
    public function getItemByPK(string $pk)
    {
        return $this->model::where('pk', '=', $pk)->first() ?? new $this->model;
    }

    /**
     * Сохраняем объект.
     *
     * @param array $data
     *
     * @return mixed
     */
    public function saveInstagramObject(array $data)
    {
        $item = $this->getItemByPK($data['pk']);
        $item->fill($data);
        $item->save();

        return $item;
    }
}
