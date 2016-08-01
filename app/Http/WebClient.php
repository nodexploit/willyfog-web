<?php


namespace App\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * HTTP client that is client_credential authorized
 *
 * Class WebClient
 * @package App\Http
 */
class WebClient extends \GuzzleHttp\Client
{
    public function __construct(array $config = [])
    {
        parent::__construct(
            array_replace($config, [
                'base_uri'      => API_URI
            ])
        );
    }

    /**
     * TODO: handle properly 401 response
     *
     * @param string $method
     * @param null $uri
     * @param array $options
     * @return mixed|null|\Psr\Http\Message\ResponseInterface
     */
    public function request($method, $uri = null, array $options = [])
    {
        $session = new Session();
        $access_token = $session->get(WEB_SESSION_KEY);

        if ($access_token == null) {
            $access_token = $this->issueWebAccessToken();
        }
        
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
                // Clean session and retry the request. possible loop
                $session->delete(WEB_SESSION_KEY);
                return $this->request($method, $uri, $options);
            }
        }

        return $res;
    }

    /**
     * TODO: handle errors
     */
    private function issueWebAccessToken()
    {
        $res = (new Client())->request('POST', OPENID_URI . '/token', [
            'form_params' => [
                'grant_type'    => 'client_credentials',
                'client_id'     => API_CLIENT,
                'client_secret' => API_SECRET
            ]
        ]);

        $api_response = json_decode($res->getBody());

        return $api_response->access_token;
    }
}
