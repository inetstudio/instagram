<?php

namespace InetStudio\Instagram\Posts\Repositories;

use InetStudio\AdminPanel\Repositories\BaseRepository;
use InetStudio\Instagram\Posts\Contracts\Models\PostModelContract;
use InetStudio\Instagram\Posts\Contracts\Repositories\PostsRepositoryContract;

/**
 * Class PostsRepository.
 */
class PostsRepository extends BaseRepository implements PostsRepositoryContract
{
    /**
     * PostsRepository constructor.
     *
     * @param PostModelContract $model
     */
    public function __construct(PostModelContract $model)
    {
        $this->model = $model;

        $this->defaultColumns = ['id', 'pk', 'user_pk', 'additional_info'];
        $this->relations = [
            'user' => function ($query) {
                $query->select(['id', 'pk', 'additional_info']);
            },
            'comments' => function ($query) {
                $query->select(['id', 'pk', 'post_pk', 'user_pk', 'additional_info']);
            },
            'media' => function ($query) {
                $query->select(['id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk', 'conversions_disk', 'uuid', 'mime_type', 'custom_properties', 'responsive_images']);
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
