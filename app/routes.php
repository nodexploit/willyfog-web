<?php

$namespace = '\App\Http\Controllers';

$authenticate = new \App\Http\Middleware\AuthenticateMiddleware();

$app->group('', function () use ($namespace) {
    $this->get('/login', "$namespace\\LoginController:showLogin");
    $this->get('/openid', "$namespace\\LoginController:openid");
    $this->get('/login/callback', "$namespace\\LoginController:loginCallback");
    $this->get('/logout', "$namespace\\LoginController:logout");
});

$app->group('', function () use ($namespace) {
    $this->get('/', "$namespace\\HomeController:hello");
})->add($authenticate);
