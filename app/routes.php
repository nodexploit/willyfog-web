<?php

$namespace = '\App\Http\Controllers';

$guest = new \App\Http\Middleware\GuestMiddleware();
$authenticate = new \App\Http\Middleware\AuthenticateMiddleware();

$app->group('', function () use ($namespace) {
    $this->get('/countries/{id}/cities', "$namespace\\CountryController:cities");
    $this->get('/cities/{id}/universities', "$namespace\\CityController:universities");
    $this->get('/universities/{id}/centres', "$namespace\\UniversityController:centres");
    $this->get('/centres/{id}/degrees', "$namespace\\CentreController:degrees");
    $this->get('/degrees/{id}/subjects', "$namespace\\DegreeController:subjects");
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

    $this->get('/requests/new', "$namespace\\RequestController:form");
    $this->post('/requests/new', "$namespace\\RequestController:create");
    $this->get('/requests/{id}', "$namespace\\RequestController:show");
    $this->post('/requests/{id}/comment', "$namespace\\RequestController:comment");
    $this->post('/requests/{id}/accept', "$namespace\\RequestController:accept");
    $this->post('/requests/{id}/reject', "$namespace\\RequestController:reject");

    $this->get('/users/{id}/notifications', "$namespace\\UserController:notifications");

    $this->get('/search', "$namespace\\SearchController:results");
})->add($authenticate);
