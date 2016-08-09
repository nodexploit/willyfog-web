<?php


namespace App\Http\Controllers;

use App\Http\AuthorizedClient;
use Slim\Http\Request;
use Slim\Http\Response;
use Interop\Container\ContainerInterface;
use App\Http\WebClient;

class UserController
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    public function notifications(Request $request, Response $response, array $args)
    {
        $user_id = $args['id'];

        $res = (new AuthorizedClient())->request('GET', "/api/v1/users/$user_id/notifications");

        $api_response = json_decode($res->getBody());

        return $response->withJson($api_response);
    }

    public function showRegisterRecognizer(Request $request, Response $response, $args)
    {
        return $this->ci->get('view')->render($response, 'recognizer/register.twig', [
            'universities' => \App\Models\University::all()
        ]);
    }

    public function showRegisterCoordinator(Request $request, Response $response, $args)
    {
        return $this->ci->get('view')->render($response, 'coordinator/register.twig', [
            'universities' => \App\Models\University::all()
        ]);
    }

    public function registerRecognizer(Request $request, Response $response, array $args)
    {
        $params = $request->getParsedBody();

        $res = (new WebClient())->request('POST', '/api/v1/users/register', [
            'form_params' => [
                'name'      => $params['name'],
                'surname'   => $params['surname'],
                'nif'       => $params['nif'],
                'email'     => $params['email'],
                'digest'    => $params['password'],
                'degree_id' => $params['degree_id'],
                'role_id'   => 3
            ]
        ]);

        $api_response = json_decode($res->getBody());

        if ($api_response->status == "Success") {
            return $response->withStatus(302)->withHeader('Location', '/');
        } else {
            $this->ci->get('flash')->addMessage('error', $api_response->status);
            $this->ci->get('flash')->addMessage('messages', implode(', ', $api_response->messages));

            return $response->withStatus(302)->withHeader('Location', '/register');
        }
    }

    public function registerCoordinator(Request $request, Response $response, array $args)
    {
        $params = $request->getParsedBody();

        $res = (new WebClient())->request('POST', '/api/v1/users/register', [
            'form_params' => [
                'name'      => $params['name'],
                'surname'   => $params['surname'],
                'nif'       => $params['nif'],
                'email'     => $params['email'],
                'digest'    => $params['password'],
                'degree_id' => $params['degree_id'],
                'role_id'   => 2
            ]
        ]);

        $api_response = json_decode($res->getBody());

        if ($api_response->status == "Success") {
            return $response->withStatus(302)->withHeader('Location', '/');
        } else {
            $this->ci->get('flash')->addMessage('error', $api_response->status);
            $this->ci->get('flash')->addMessage('messages', implode(', ', $api_response->messages));

            return $response->withStatus(302)->withHeader('Location', '/register');
        }
    }
}