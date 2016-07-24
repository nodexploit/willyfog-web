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
}
