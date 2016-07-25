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
        $res = (new \App\Http\Client())->request('GET', '/api/v1/users/1');

        $user_info = $res->getBody();

        return $this->ci->get('view')->render($response, 'home.twig', [
            'user_info' => $user_info
        ]);
    }
}
