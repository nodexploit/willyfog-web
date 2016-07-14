<?php


namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class HomeController
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    public function hello(Request $request,Response $response, $args)
    {
        return $this->ci->get('view')->render($response, 'home.twig');
    }

    public function login(Request $request, Response $response, $args)
    {
        $client_id = 'testclient';
        $redirect_uri = 'http://192.168.33.10/login/callback';
        $response_type = 'code';
        $state = 'xyz';
        $scope = 'openid';
        
        return $response->withRedirect(
            "http://192.168.33.10:8000/authorize?client_id=$client_id&redirect_uri=$redirect_uri&response_type=$response_type&scope=$scope&state=$state"
        );
    }

    public function loginCallback(Request $request, Response $response, $args)
    {
        $code = $request->getQueryParam('code');

        $res = (new Client())->request('POST', 'http://192.168.33.10:8000/token', [
            'form_params' => [
                'grant_type'    => 'authorization_code',
                'client_id'     => 'testclient',
                'code'          => $code,
                'redirect_uri'  => 'http://192.168.33.10/login/callback'
            ]
        ]);

        $response = $res->getBody();

        $id = base64_decode(json_decode($response)->id_token);

        return "Here is your $id";
    }
}
