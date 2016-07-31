<?php

$namespace = '\App\Http\Controllers';

$guest = new \App\Http\Middleware\GuestMiddleware();
$authenticate = new \App\Http\Middleware\AuthenticateMiddleware();

$app->group('', function () use ($namespace) {
    $this->get('/login', "$namespace\\LoginController:showLogin");
    $this->get('/openid', "$namespace\\LoginController:openid");
    $this->get('/login/callback', "$namespace\\LoginController:loginCallback");
})->add($guest);

$app->group('', function () use ($namespace) {
    $this->get('/logout', "$namespace\\LoginController:logout");

    $this->get('/', "$namespace\\RequestController:index");
    $this->get('/request/{id}', "$namespace\\RequestController:show");
})->add($authenticate);
