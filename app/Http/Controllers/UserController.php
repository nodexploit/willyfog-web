<?php


namespace App\Http\Controllers;

use App\Http\Auth;
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

    public function register(Request $request, Response $response, $args)
    {
        $params = $request->getParsedBody();

        if (empty($params['password']) || $params['password'] != $params['password_confirmation']) {
            return $this->checkPasswordConfirmation($response, $params, '/register');
        }

        $api_response = $this->registerUser($params, Role::$STUDENT);

        return $this->handleApiRegisterResponse(
            $response,
            $api_response,
            $params,
            'User successfully created. Please log in.',
            '/register'
        );
    }

    public function showRegisterRecognizer(Request $request, Response $response, $args)
    {
        $auth = Auth::getInstance($this->ci);

        if (!$auth->isCoordinator()) {
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        return $this->ci->get('view')->render($response, 'recognizer/register.twig');
    }

    public function showRegisterCoordinator(Request $request, Response $response, $args)
    {
        $auth = Auth::getInstance($this->ci);

        if (!$auth->isAdmin()) {
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        return $this->ci->get('view')->render($response, 'coordinator/register.twig', [
            'universities' => \App\Models\University::all()
        ]);
    }

    public function registerRecognizer(Request $request, Response $response, array $args)
    {
        $params = $request->getParsedBody();

        if (empty($params['password']) || $params['password'] != $params['password_confirmation']) {
            return $this->checkPasswordConfirmation($response, $params, '/users/register/recognizer');
        }

        $api_response = $this->registerUser($params, Role::$RECOG);

        return $this->handleApiRegisterResponse(
            $response,
            $api_response,
            $params,
            'Recognizer successfully created.',
            '/users/register/recognizer'
        );
    }

    public function registerCoordinator(Request $request, Response $response, array $args)
    {
        $params = $request->getParsedBody();

        if (empty($params['password']) || $params['password'] != $params['password_confirmation']) {
            return $this->checkPasswordConfirmation($response, $params, '/users/register/coordinator');
        }

        $api_response = $this->registerUser($params, Role::$COORD);

        return $this->handleApiRegisterResponse(
            $response,
            $api_response,
            $params,
            'Coordinator successfully created.',
            '/users/register/coordinator'
        );
    }

    private function checkPasswordConfirmation(Response $response, $params, $callback_url)
    {
        $this->ci->get('flash')->addMessage('error', 'Your password does not equals to your confirmation.');
        $this->ci->get('session')->set('params', $params);

        return $response->withStatus(302)->withHeader('Location', $callback_url);
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
                'centre_id' => $params['centre_id'],
                'role_id'   => $role_id
            ]
        ]);

        return json_decode($res->getBody());
    }

    private function handleApiRegisterResponse(
        Response $response,
        $api_response,
        $params,
        $success_msg,
        $callback_url
    ) {
        if ($api_response->status == "Success") {
            $this->ci->get('flash')->addMessage('success', $success_msg);

            return $response->withStatus(302)->withHeader('Location', '/');
        } else {
            $this->ci->get('flash')->addMessage('error', $api_response->status);
            $this->ci->get('flash')->addMessage('messages', implode(', ', $api_response->messages));
            $this->ci->get('session')->set('params', $params);

            return $response->withStatus(302)->withHeader('Location', $callback_url);
        }
    }
}