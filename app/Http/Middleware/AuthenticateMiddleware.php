<?php


namespace App\Http\Middleware;


use Dflydev\FigCookies\FigRequestCookies;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class AuthenticateMiddleware
{
    /**
     * Invoke middleware
     *
     * @param  RequestInterface  $request  PSR7 request object
     * @param  ResponseInterface $response PSR7 response object
     * @param  callable          $next     Next middleware callable
     *
     * @return ResponseInterface PSR7 response object
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next)
    {
        $cookie = FigRequestCookies::get($request, COOKIE_KEY);

        if ($cookie->getValue() === null) {
            $response = $response->withRedirect('/guest');
        } else {
            $response = $next($request, $response);
        }

        return $response;
    }
}