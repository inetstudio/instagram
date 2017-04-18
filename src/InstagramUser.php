<?php

namespace InetStudio\Instagram;

use GuzzleHttp\Client;
use Emojione\Emojione as Emoji;
use InetStudio\Instagram\Models\InstagramUserModel;

class InstagramUser
{
    /**
     * Создание пользователя по его идентификатору.
     *
     * @param string $id
     * @return null
     */
    public function createUser($id = '')
    {
        if (! $id) {
            return;
        }

        $result = $this->sendRequest('getUsernameInfo', [$id]);

        if (isset($result['user'])) {
            $user = $result['user'];
        } else {
            return;
        }

        $instagramUser = InstagramUserModel::updateOrCreate([
            'pk' => $user['pk'],
        ], [
            'username' => $user['username'],
            'full_name' => Emoji::toShort($user['full_name']),
            'profile_pic_url' => (isset($user['hd_profile_pic_versions'][0]['url'])) ? $user['hd_profile_pic_versions'][0]['url'] : $user['profile_pic_url'],
            'follower_count' => isset($user['follower_count']) ? $user['follower_count'] : 0,
            'following_count' => isset($user['following_count']) ? $user['following_count'] : 0,
            'media_count' => isset($user['media_count']) ? $user['media_count'] : 0,
        ]);

        return $instagramUser;
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
}
