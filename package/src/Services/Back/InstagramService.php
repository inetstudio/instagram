<?php

namespace InetStudio\Instagram\Services\Back;

use InstagramAPI\Instagram;
use InetStudio\Instagram\Contracts\Services\Back\InstagramServiceContract;

/**
 * Class InstagramService.
 */
class InstagramService implements InstagramServiceContract
{
    /**
     * @var Instagram
     */
    protected $instagram;

    /**
     * InstagramService constructor.
     */
    public function __construct()
    {
        $username = config('services.instagram_api.username');
        $password = config('services.instagram_api.password');

        Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
        $this->instagram = new Instagram(false, false);
        $this->instagram->login($username, $password);
    }

    /**
     * Запрос в инстаграм.
     *
     * @param string $collection
     * @param string $method
     * @param array $params
     *
     * @return mixed
     */
    public function request(string $collection, string $method, array $params = [])
    {
        $result = call_user_func_array(array($this->instagram->$collection, $method), $params);

        return $result;
    }
}
