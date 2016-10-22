<?php


namespace App\Http\Controllers;

use App\Http\AuthorizedClient;
use App\Models\Role;
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

    public function showRegister(Request $request, Response $response, $args)
    {
        return $this->ci->get('view')->render($response, 'register.twig', [
            'universities' => \App\Models\University::all()
        ]);
    }

    /**
     * TODO: handle registration failure
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return static
     */
    public function register(Request $request, Response $response, $args)
    {
        $params = $request->getParsedBody();

        if ($params['password'] != $params['password_confirmation']) {
            $this->ci->get('flash')->addMessage('error', 'Your password does not equals to your confirmation.');

            return $response->withStatus(302)->withHeader('Location', '/register');
        }

        $api_response = $this->registerUser($params, Role::$STUDENT);

        if ($api_response->status == "Success") {
            $this->ci->get('flash')->addMessage('success', 'User successfully created. Please log in.');

            return $response->withStatus(302)->withHeader('Location', '/');
        } else {
            $this->ci->get('flash')->addMessage('error', $api_response->status);
            $this->ci->get('flash')->addMessage('messages', implode(', ', $api_response->messages));
            $this->ci->get('session')->set('params', $params);

            return $response->withStatus(302)->withHeader('Location', '/register');
        }
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
                'role_id'   => Role::$RECOG
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
                'role_id'   => Role::$COORD
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

    private function registerUser($params, $role_id)
    {
        $res = (new WebClient([
            'base_uri'  => OPENID_URI
        ], false))->request('POST', '/api/v1/users/new', [
            'form_params' => [
                'name'      => $params['name'],
                'surname'   => $params['surname'],
                'nif'       => $params['nif'],
                'email'     => $params['email'],
                'digest'    => $params['password'],
                'degree_id' => $params['degree_id'],
                'role_id'   => $role_id
            ]
        ]);

        return json_decode($res->getBody());
    }
}