<?php


namespace App\Http;


use Interop\Container\ContainerInterface;

class Auth
{
    private static $instance;
    private $ci;

    private function __construct() {}

    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function setCI(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    /**
     * Returns whether there is an authenticated user in the system.
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        $auth = $this->getAuthSession();

        return $auth !== null;
    }

    private function getAuthSession()
    {
        return $this->ci
            ->get('session')
            ->get(SESSION_KEY);
    }

    /**
     * Returns info about the authenticated user.
     *
     * TODO: handle request errors. Cache with redis instead of session
     */
    public function user()
    {
        if (!$this->isLoggedIn()) {
            throw new \Exception('User not already logged in.');
        }

        $cached_user_info = $this->getCachedUserInfo();

        if ($cached_user_info === null) {
            $auth = $this->getAuthSession();
            $user_id = $auth['user_id'];

            $res = (new AuthorizedClient())->request('GET', "/api/v1/users/$user_id");

            $user_info = json_decode($res->getBody());

            $cached_user_info = $this->cacheUserInfo($user_info);
        }

        return $cached_user_info;
    }
    
    private function cacheUserInfo($user_info)
    {
        $this->ci
            ->get('session')
            ->set(UI_SESSION_KEY, $user_info);

        return $user_info;
    }
    
    private function getCachedUserInfo()
    {
        return $this->ci
            ->get('session')
            ->get(UI_SESSION_KEY);
    }
}