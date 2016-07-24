<?php


namespace App\Http\Controllers;

use App\Http\Session;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Firebase\JWT\JWT;
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

    public function showLogin(Request $request, Response $response, $args)
    {
        return $this->ci->get('view')->render($response, 'login.twig');
    }

    public function openid(Request $request, Response $response, $args)
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

        // TODO: handle errors
        $res = (new Client())->request('POST', 'http://openid.willyfog.com/token', [
            'form_params' => [
                'grant_type'    => 'authorization_code',
                'client_id'     => API_CLIENT,
                'client_secret' => API_SECRET,
                'code'          => $code,
                'redirect_uri'  => 'http://willyfog.com/login/callback'
            ]
        ]);

        $api_response = json_decode($res->getBody());

        // Decode the response
        $access_token = $api_response->access_token;
        $id_token = $api_response->id_token;

        // Decode the JWT to get the user_id. TODO: abort if not secure
        $decoded_jwt = $this->decodeJWT($id_token);
        $user_id = $decoded_jwt->sub;

        $this->setSession($user_id, $access_token);

        // Set a redirect to the homepage
        $response = $response->withRedirect('/');

        // Save JWT into the cookie
        $response = $this->setCookie($response, $id_token);

        return $response;
    }

    public function logout(Request $request, Response $response, $args)
    {
        $this->unsetSession();
        $response = $response->withRedirect('/login');

        return $this->unsetCookie($response);
    }

    /**
     * TODO: handle absence of pubkey.pem
     *
     * @param $id_token
     * @return object
     */
    private function decodeJWT($id_token)
    {
        $key = file_get_contents(APP_PATH . '/data/pubkey.pem');

        return JWT::decode($id_token, $key, ['RS256']);
    }

    private function setSession($user_id, $access_token)
    {
        $session = new Session();
        $session->set('auth', [
            'user_id'       => $user_id,
            'access_token'  => $access_token
        ]);
    }

    private function unsetSession()
    {
        Session::destroy();
    }

    private function setCookie($response, $id_token)
    {
        $cookie = SetCookie::create('willyfog_session')
            ->withValue($id_token)
            ->withPath('/')
            ->withDomain('.willyfog.com');

        return FigResponseCookies::set($response, $cookie);
    }

    private function unsetCookie($response)
    {
        return FigResponseCookies::remove($response, 'willyfog_session');
    }
}
