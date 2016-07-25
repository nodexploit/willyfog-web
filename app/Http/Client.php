<?php


namespace App\Http;

use GuzzleHttp\Exception\RequestException;

class Client extends \GuzzleHttp\Client
{
    public function __construct(array $config = [])
    {
        parent::__construct(
            array_replace($config, [
                'base_uri'      => API_URI
            ])
        );
    }

    public function request($method, $uri = null, array $options = [])
    {
        $access_token = (new Session())->get(SESSION_KEY)['access_token'];
        
        $options = array_merge($options, [
            'headers'   => [
                'Authorization' => "Bearer $access_token"
            ]
        ]);

        $res = null;
        try {
            $res = parent::request($method, $uri, $options);
        } catch (RequestException $e) {
            if ($e->getResponse()->getStatusCode() == 401) { // Access token expired
                header('Location: /logout');
                die;
            }
        }

        return $res;
    }
}
