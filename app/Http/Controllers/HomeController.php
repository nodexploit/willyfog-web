<?php


namespace App\Http\Controllers;

use App\Http\Auth;
use App\Http\AuthorizedClient;
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

    public function hello(Request $request, Response $response, $args)
    {
        $res = (new AuthorizedClient)->request('GET', '/api/v1/equivalences');

        $equivalences = json_decode($res->getBody());

        return $this->ci->get('view')->render($response, 'home.twig', [
            'equivalences' => $equivalences
        ]);
    }
}
