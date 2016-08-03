<?php

$namespace = '\App\Http\Controllers';

$guest = new \App\Http\Middleware\GuestMiddleware();
$authenticate = new \App\Http\Middleware\AuthenticateMiddleware();

$app->group('', function () use ($namespace) {
    $this->get('/universities/{id}/centres', "$namespace\\UniversityController:centres");
    $this->get('/centres/{id}/degrees', "$namespace\\CentreController:degrees");
});

$app->group('', function () use ($namespace) {
    $this->get('/guest', "$namespace\\LoginController:showWelcome");
    $this->get('/register', "$namespace\\LoginController:showRegister");
    $this->post('/register', "$namespace\\LoginController:register");
    $this->get('/openid', "$namespace\\LoginController:openid");
    $this->get('/login/callback', "$namespace\\LoginController:loginCallback");
})->add($guest);

$app->group('', function () use ($namespace) {
    $this->get('/logout', "$namespace\\LoginController:logout");

    $this->get('/', "$namespace\\RequestController:index");

    $this->get('/requests/new', "$namespace\\RequestController:create");
    $this->get('/requests/{id}', "$namespace\\RequestController:show");
})->add($authenticate);
