<?php

namespace InetStudio\Instagram\Services\Back;

use GuzzleHttp\Client;
use InstagramAPI\Instagram;
use InetStudio\Instagram\Contracts\Services\Back\InstagramServiceContract;

/**
 * Class InstagramService.
 */
class InstagramService implements InstagramServiceContract
{
    protected array $params = [
        'serialize' => 1,
    ];

    protected Instagram $instagram;

    protected bool $useExternalService = false;

    /**
     * InstagramService constructor.
     */
    public function __construct()
    {
        $this->params['username'] = config('services.instagram_api.username');
        $this->params['password'] = config('services.instagram_api.password');

        if (! config('services.instagram_api.url', '')) {
            Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
            $this->instagram = new Instagram(false, false);
            $this->instagram->login($this->params['username'], $this->params['password']);
        } else {
            $this->useExternalService = true;
        }
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
        if ($this->useExternalService) {
            $client = new Client();

            $url = trim(config('services.instagram_api.url'), '/').'/'.$collection.'/'.$method;

            $response = $client->request(
                'POST',
                $url,
                [
                    'headers' => [
                        'Authorization' => 'Bearer '.config('services.instagram_api.token'),
                        'Accept' => 'application/json',
                    ],
                    'form_params' => [
                        'options' => $this->params,
                        'data' => $params,
                    ],
                ]
            );

            $response = json_decode($response->getBody()->getContents(), true);
            $result = ($this->params['serialize']) ? unserialize($response['result']) : $response['result'];
        } else {
            $result = call_user_func_array(array($this->instagram->$collection, $method), $params);
        }

        return $result;
    }
}
