<?php

namespace InetStudio\Instagram\Stories\Services\Back;

use InstagramAPI\Signatures;
use InstagramAPI\InstagramID;
use InstagramAPI\Response\Model\Item;
use InstagramAPI\Response\Model\SectionMedia;
use InstagramAPI\Response\Model\Image_Versions2;
use InetStudio\AdminPanel\Services\Back\BaseService;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use InetStudio\Instagram\Stories\Contracts\Models\StoryModelContract;
use InetStudio\Instagram\Stories\Contracts\Services\Back\StoriesServiceContract;

/**
 * Class StoriesService.
 */
class StoriesService extends BaseService implements StoriesServiceContract
{
    /**
     * StoriesService constructor.
     */
    public function __construct()
    {
        parent::__construct(app()->make('InetStudio\Instagram\Stories\Contracts\Repositories\StoriesRepositoryContract'));
    }

    /**
     * Сохраняем модель.
     *
     * @param Item $story
     *
     * @return StoryModelContract
     */
    public function save(Item $story): StoryModelContract
    {
        $data = [
            'pk' => $story->getPk(),
            'user_pk' => $story->getUser()->getPk(),
            'additional_info' => json_decode(json_encode($story), true),
        ];

        $item = $this->repository->saveInstagramObject($data, $story->getPk());
        $this->attachMedia($item, $story);

        return $item;
    }


    /**
     * Аттачим медиа к модели.
     *
     * @param StoryModelContract $item
     * @param Item $story
     */
    protected function attachMedia(StoryModelContract $item, Item $story): void
    {
        $currentMedia = $item->getMedia('media')->pluck('name')->toArray();

        switch ($story->getMediaType()) {
            case 1:
                $this->attachImage($item, $story->getImageVersions2(), $story->getId(), 'media', $currentMedia);
                break;
            case 2:
                $this->attachVideo($item, $story->getImageVersions2(), $story->getVideoVersions(), $story->getId(), $currentMedia);
                break;
            case 8:
                foreach ($story->getCarouselMedia() as $carouselMedia) {
                    switch ($carouselMedia->getMediaType()) {
                        case 1:
                            $this->attachImage($item, $carouselMedia->getImageVersions2(), $carouselMedia->getId(), 'media', $currentMedia);
                            break;
                        case 2:
                            $this->attachVideo($item, $carouselMedia->getImageVersions2(), $carouselMedia->getVideoVersions(), $carouselMedia->getId(), $currentMedia);
                            break;
                    }
                }
                break;
        }
    }

    /**
     * Аттачим фото к модели.
     *
     * @param StoryModelContract $item
     * @param Image_Versions2 $image
     * @param string $name
     * @param string $collection
     * @param array $currentMedia
     *
     * @return Media
     */
    protected function attachImage(StoryModelContract $item,
        Image_Versions2 $image,
        string $name,
        string $collection = 'media',
        array $currentMedia = []): ?Media
    {
        $imageAttach = null;

        $imageCandidate = $image->getCandidates()[0];

        if ($imageCandidate->getUrl() && ! in_array($name, $currentMedia)) {
            $imageAttach = $item->addMediaFromUrl($imageCandidate->getUrl())
                ->usingName($name)
                ->withCustomProperties(json_decode(json_encode($image), true))
                ->toMediaCollection($collection, 'instagram_stories');
        }

        return $imageAttach;
    }

    /**
     * Аттачим видео к модели.
     *
     * @param StoryModelContract $item
     * @param Image_Versions2 $image
     * @param array $video
     * @param string $name
     * @param array $currentMedia
     *
     * @return Media
     */
    protected function attachVideo(StoryModelContract $item,
        Image_Versions2 $image,
        array $video,
        string $name,
        array $currentMedia = []): ?Media
    {
        $videoAttach = null;

        $cover = $this->attachImage($item, $image, $name, 'cover', $currentMedia);

        $videoVersion = $video[0];

        if ($cover && $videoVersion->getUrl() && ! in_array($name, $currentMedia)) {
            $videoAttach = $item->addMediaFromUrl($videoVersion->getUrl())
                ->usingName($name)
                ->withCustomProperties(array_merge(
                        json_decode(json_encode($image), true),
                        [
                            'cover' => [
                                'model' => get_class($cover),
                                'id' => $cover->id,
                            ],
                        ])
                )
                ->toMediaCollection('media', 'instagram_stories');
        }

        return $videoAttach;
    }

    /**
     * Поиск постов по тегу и их фильтрация.
     *
     * @param mixed $tag
     * @param array $filters
     *
     * @return array
     */
    public function getStoriesByTag($tag, array $filters = []): array
    {
        $instagramService = app()->make('InetStudio\Instagram\Contracts\Services\Back\InstagramServiceContract');

        $hashtag = (is_array($tag)) ? array_values($tag)[0] : $tag;

        $result = $instagramService->request('hashtag', 'getStory', [$hashtag]);
        $processedResult = $this->processResult($result, $filters);

        return $processedResult['items'];
    }

    /**
     * Обрабатываем результат запроса.
     *
     * @param $result
     * @param $filters
     *
     * @return array
     */
    protected function processResult($result, $filters): array
    {
        $data = [
            'stop' => false,
            'items' => [],
        ];

        if ($story = $result->getStory()) {
            if ($items = $story->getItems()) {
                $filtered = $this->filterStories($items, $filters);

                $data['stop'] = $filtered['stop'];
                $data['items'] = array_merge($data['items'], $filtered['items']);
            }
        }

        return $data;
    }

    /**
     * Фильтрация историй.
     *
     * @param array $items
     * @param array $filters
     *
     * @return array
     */
    protected function filterStories(array $items, array $filters = []): array
    {
        $filteredStories = [
            'items' => [],
            'stop' => false,
        ];

        $pipeLine = app('Illuminate\Pipeline\Pipeline');
        foreach ($items as $item) {
            if ($item instanceof SectionMedia) {
                $item = $item->getMedia();
            }

            if (isset($filters['startTime']) && $filters['startTime']->startTime && (int) $item->getTakenAt() < $filters['startTime']->startTime) {
                $filteredStories['stop'] = true;

                break;
            }

            $item = $pipeLine
                ->send($item)
                ->through($filters)
                ->then(function ($story) {
                    return $story;
                });

            if (! $item) {
                continue;
            }

            array_push($filteredStories['items'], $item);
        }

        return $filteredStories;
    }
}
