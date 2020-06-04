<?php

namespace InetStudio\Instagram\Posts\Services\Back;

use InstagramAPI\Signatures;
use InstagramAPI\InstagramID;
use InstagramAPI\Response\Model\Item;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use InstagramAPI\Response\Model\SectionMedia;
use InstagramAPI\Response\Model\Image_Versions2;
use InetStudio\AdminPanel\Services\Back\BaseService;
use InetStudio\Instagram\Posts\Contracts\Models\PostModelContract;
use InetStudio\Instagram\Posts\Contracts\Services\Back\PostsServiceContract;

/**
 * Class PostsService.
 */
class PostsService extends BaseService implements PostsServiceContract
{
    /**
     * PostsService constructor.
     */
    public function __construct()
    {
        parent::__construct(app()->make('InetStudio\Instagram\Posts\Contracts\Repositories\PostsRepositoryContract'));
    }

    /**
     * Сохраняем модель.
     *
     * @param Item $post
     *
     * @return PostModelContract
     */
    public function save(Item $post): PostModelContract
    {
        $data = [
            'pk' => $post->getPk(),
            'user_pk' => $post->getUser()->getPk(),
            'additional_info' => json_decode(json_encode($post), true),
        ];

        $item = $this->repository->saveInstagramObject($data, $post->getPk());
        $this->attachMedia($item, $post);

        return $item;
    }


    /**
     * Аттачим медиа к модели.
     *
     * @param PostModelContract $item
     * @param Item $post
     */
    protected function attachMedia(PostModelContract $item, Item $post): void
    {
        $currentMedia = $item->getMedia('media')->pluck('name')->toArray();

        switch ($post->getMediaType()) {
            case 1:
                $this->attachImage($item, $post->getImageVersions2(), $post->getId(), 'media', $currentMedia);
                break;
            case 2:
                $this->attachVideo($item, $post->getImageVersions2(), $post->getVideoVersions(), $post->getId(), $currentMedia);
                break;
            case 8:
                foreach ($post->getCarouselMedia() as $carouselMedia) {
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
     * @param PostModelContract $item
     * @param Image_Versions2 $image
     * @param string $name
     * @param string $collection
     * @param array $currentMedia
     *
     * @return Media
     */
    protected function attachImage(PostModelContract $item,
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
                ->toMediaCollection($collection, 'instagram_posts');
        }

        return $imageAttach;
    }

    /**
     * Аттачим видео к модели.
     *
     * @param PostModelContract $item
     * @param Image_Versions2 $image
     * @param array $video
     * @param string $name
     * @param array $currentMedia
     *
     * @return Media
     */
    protected function attachVideo(PostModelContract $item,
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
                ->toMediaCollection('media', 'instagram_posts');
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
    public function getPostsByTag($tag, array $filters = []): array
    {
        $instagramService = app()->make('InetStudio\Instagram\Contracts\Services\Back\InstagramServiceContract');

        $postsArr = [];

        foreach (['top', 'recent'] as $tab) {
            $rankToken = Signatures::generateUUID();

            $haveData = true;
            $stop = false;

            $nextMediaIds = null;
            $maxId = null;

            $hashtag = (is_array($tag)) ? array_values($tag)[0] : $tag;

            while ($haveData && ! $stop) {
                $result = $instagramService->request('hashtag', 'getSection', [$hashtag, $rankToken, $tab, $nextMediaIds, $maxId]);
                sleep(5);

                $processedResult = $this->processResult($result, $filters);

                $stop = $processedResult['stop'];
                $postsArr = array_merge($postsArr, $processedResult['items']);

                $haveData = (!! $result->getNextMaxId());
                $maxId = $result->getNextMaxId() ?? null;
            }

        }

        return $postsArr;
    }

    /**
     * Обрабатываем результат запроса.
     *
     * @param $result
     *
     * @return array
     */
    protected function processResult($result, $filters): array
    {
        $data = [
            'stop' => false,
            'items' => [],
        ];

        if ($result->isSections()) {
            $sections = $result->getSections();

            foreach ($sections as $section) {

                if ($section->isItems()) {
                    if ($items = $section->getItems()) {
                        $filtered = $this->filterPosts($items, $filters);

                        $data['stop'] = $filtered['stop'];
                        $data['items'] = array_merge($data['items'], $filtered['posts']);
                    }
                }

                if ($section->isLayoutContent()) {
                    $layoutContent = $section->getLayoutContent();

                    if ($items = $layoutContent->getMedias()) {
                        $filtered = $this->filterPosts($items, $filters);

                        $data['stop'] = $filtered['stop'];
                        $data['items'] = array_merge($data['items'], $filtered['posts']);
                    }
                }
            }
        }

        if ($result->isRankedItems()) {
            if ($items = $result->getRankedItems()) {
                $filtered = $this->filterPosts($items, $filters);

                $data['stop'] = $filtered['stop'];
                $data['items'] = array_merge($data['items'], $filtered['posts']);
            }
        }

        if ($result->isItems()) {
            if ($items = $result->getItems()) {
                $filtered = $this->filterPosts($items, $filters);

                $data['stop'] = $filtered['stop'];
                $data['items'] = array_merge($data['items'], $filtered['posts']);
            }
        }

        return $data;
    }

    /**
     * Фильтрация постов.
     *
     * @param array $posts
     * @param array $filters
     *
     * @return array
     */
    protected function filterPosts(array $posts, array $filters = []): array
    {
        $filteredPosts = [];
        $filteredPosts['posts'] = [];
        $filteredPosts['stop'] = false;

        $pipeLine = app('Illuminate\Pipeline\Pipeline');
        foreach ($posts as $post) {
            if ($post instanceof SectionMedia) {
                $post = $post->getMedia();
            }

            if (isset($filters['startTime']) && $filters['startTime']->startTime && (int) $post->getTakenAt() < $filters['startTime']->startTime) {
                $filteredPosts['stop'] = true;

                break;
            }

            $post = $pipeLine
                ->send($post)
                ->through($filters)
                ->then(function ($post) {
                    return $post;
                });

            if (! $post) {
                continue;
            }

            array_push($filteredPosts['posts'], $post);
        }

        return $filteredPosts;
    }

    /**
     * Получаем пост по коду.
     *
     * @param string $code
     *
     * @return Item|null
     */
    public function getPostByCode(string $code): ?Item
    {
        $instagramService = app()->make('InetStudio\Instagram\Contracts\Services\Back\InstagramServiceContract');

        $id = InstagramID::fromCode($code);

        $result = $instagramService->request('media', 'getInfo', [$id]);

        $posts = $result->getItems();

        return $posts[0] ?? null;
    }
}
