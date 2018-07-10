<?php

namespace InetStudio\Instagram\Services\Back;

use GuzzleHttp\Client;
use InstagramAPI\Signatures;
use Emojione\Emojione as Emoji;
use InetStudio\Instagram\Models\InstagramPostModel;
use InetStudio\Instagram\Contracts\Services\Back\InstagramPostsServiceContract;

/**
 * Class InstagramPostsService
 * @package InetStudio\Instagram\Services\Back
 */
class InstagramPostsService implements InstagramPostsServiceContract
{
    /**
     * Создание поста по его идентификатору.
     *
     * @param string $id
     *
     * @return InstagramPostModel|null
     */
    public function createPost(string $id = ''): ?InstagramPostModel
    {
        $post = $this->getPostByID($id);

        if (! $post) {
            return null;
        }

        $instagramPost = InstagramPostModel::updateOrCreate([
            'pk' => $post['pk'],
        ], [
            'user_pk' => $post['user']['pk'],
            'media_type' => $post['media_type'],
            'image_versions' => $post['image_versions2']['candidates'][0]['url'],
            'video_versions' => (isset($post['video_versions'][0]['url'])) ? $post['video_versions'][0]['url'] : '',
            'code' => $post['code'],
            'view_count' => isset($post['view_count']) ? $post['view_count'] : 0,
            'comment_count' => $post['comment_count'],
            'like_count' => $post['like_count'],
            'caption' => (isset($post['caption']['text'])) ? Emoji::toShort($post['caption']['text']) : '',
            'taken_at' => $post['taken_at'],
        ]);

        return $instagramPost;
    }

    /**
     * Поиск постов по тегу и их фильтрация по времени, типу, id.
     *
     * @param mixed $tag
     * @param string $periodStart
     * @param string $periodEnd
     * @param array $filter
     * @param array $types
     *
     * @return array
     */
    public function getPostsByTag($tag, $periodStart = '', $periodEnd = '', $filter = [], $types = [1, 2])
    {
        $rankToken = Signatures::generateUUID();

        $haveData = true;
        $stop = false;

        $next = null;
        $postsArr = [];

        $startTime = ($periodStart) ? strtotime($periodStart) : null;
        $endTime = ($periodEnd) ? strtotime($periodEnd) : null;

        $searchTag = (is_array($tag)) ? array_values($tag)[0] : $tag;

        while ($haveData && ! $stop) {
            $result = $this->sendRequest('hashtag/getFeed', [$searchTag, $rankToken, $next]);
            sleep(5);

            if (isset($result['ranked_items'])) {
                $ranked = $this->getFilteredPosts($result['ranked_items'], $tag, $startTime, $endTime, $filter, $types);

                $postsArr = array_merge($postsArr, $ranked['posts']);
                $stop = $ranked['stop'];
            }

            if (isset($result['items'])) {
                $all = $this->getFilteredPosts($result['items'], $tag, $startTime, $endTime, $filter, $types);

                $postsArr = array_merge($postsArr, $all['posts']);
                $stop = $all['stop'];
            }

            $haveData = (! isset($result['next_max_id'])) ? false : true;
            $next = (isset($result['next_max_id'])) ? $result['next_max_id'] : null;
        }

        return array_reverse($postsArr);
    }

    /**
     * Получаем пост из Instagram.
     *
     * @param string $id
     *
     * @return array|null
     */
    public function getPostByID(string $id = ''): ?array
    {
        if (! $id) {
            return null;
        }

        $result = $this->sendRequest('media/getInfo', [$id]);
        sleep(5);

        if (isset($result['items'][0])) {
            $post = $result['items'][0];
        } else {
            return null;
        }

        return $post;
    }

    /**
     * Фильтрация постов.
     *
     * @param $posts
     * @param $tag
     * @param $startTime
     * @param $endTime
     * @param $filter
     * @param $types
     * @return mixed
     */
    private function getFilteredPosts($posts, $tag, $startTime, $endTime, $filter, $types)
    {
        $filteredPosts = [];

        $filteredPosts['posts'] = [];
        $filteredPosts['stop'] = false;

        $tag = $this->prepareTag($tag);

        foreach ($posts as $post) {
            if (is_array($tag)) {
                $caption = (isset($post['caption']['text'])) ? Emoji::toShort($post['caption']['text']) : '';
                // fix for changed Emoji transformers. [by Rakhmankin on 10/07/2018]
                $caption = preg_replace('/:pound_symbol:/', '#', $caption);
                preg_match_all('/(#[а-яА-Яa-zA-Z0-9]+)/u', $caption, $postTags);
                $postTags = array_map(function ($value) {
                    return mb_strtolower($value);
                }, $postTags[0]);

                if (count(array_intersect($tag, $postTags)) != count($tag)) {
                    continue;
                }
            }

            if (in_array($post['pk'], $filter) || ! in_array($post['media_type'], $types)) {
                continue;
            }

            if ($endTime && $post['taken_at'] > $endTime) {
                continue;
            }

            if ($startTime && $post['taken_at'] < $startTime) {
                $filteredPosts['stop'] = true;
                break;
            } else {
                array_push($filteredPosts['posts'], $post);
            }
        }

        return $filteredPosts;
    }

    /**
     * Запрос к сервису для получения данных.
     *
     * @param $action
     * @param $params
     * @return mixed
     */
    private function sendRequest($action, $params)
    {
        $client = new Client();
        $response = $client->post(config('instagram.services.url').$action, [
            'form_params' => $params,
        ]);

        $media = json_decode($response->getBody()->getContents(), true);

        return $media;
    }

    /**
     * Приводим полученные теги к нужному виду.
     *
     * @param $tag
     * @return array|string
     */
    private function prepareTag($tag)
    {
        if (is_array($tag)) {
            return array_map(function ($value) {
                return '#'.trim($value, '#');
            }, $tag);
        } else {
            return '#'.trim($tag, '#');
        }
    }
}
