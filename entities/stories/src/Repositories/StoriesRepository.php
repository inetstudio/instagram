<?php

namespace InetStudio\Instagram\Stories\Repositories;

use InetStudio\AdminPanel\Repositories\BaseRepository;
use InetStudio\Instagram\Stories\Contracts\Models\StoryModelContract;
use InetStudio\Instagram\Stories\Contracts\Repositories\StoriesRepositoryContract;

/**
 * Class StoriesRepository.
 */
class StoriesRepository extends BaseRepository implements StoriesRepositoryContract
{
    /**
     * StoriesRepository constructor.
     *
     * @param StoryModelContract $model
     */
    public function __construct(StoryModelContract $model)
    {
        $this->model = $model;

        $this->defaultColumns = ['id', 'pk', 'user_pk', 'additional_info'];
        $this->relations = [
            'user' => function ($query) {
                $query->select(['id', 'pk', 'additional_info']);
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
