<?php


namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class LoginController
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    public function login(Request $request, Response $response, $args)
    {
        $client_id = API_CLIENT;
        $redirect_uri = API_REDIRECT_URI;
        $response_type = 'code';
        $state = 'xyz';
        $scope = 'openid';
        
        return $response->withRedirect(
            "http://openid.willyfog.com/authorize?client_id=$client_id&redirect_uri=$redirect_uri&response_type=$response_type&scope=$scope&state=$state"
        );
    }

    public function loginCallback(Request $request, Response $response, $args)
    {
        $code = $request->getQueryParam('code');

        $res = (new Client())->request('POST', 'http://openid.willyfog.com/token', [
            'form_params' => [
                'grant_type'    => 'authorization_code',
                'client_id'     => API_CLIENT,
                'client_secret' => API_SECRET,
                'code'          => $code,
                'redirect_uri'  => 'http://willyfog.com/login/callback'
            ]
        ]);

        $response = $res->getBody();

        $json_decode = json_decode($response);
        var_dump($json_decode);

        $access_token = $json_decode->access_token;

        $id_token = $json_decode->id_token;

        echo $id_token;

        return "Here is your access token: $access_token";
    }
}
