<?php

$namespace = '\App\Http\Controllers';

$app->group('', function () use ($namespace) {
    $this->get('/countries/{id}/cities', "$namespace\\CountryController:cities");
    $this->get('/cities/{id}/universities', "$namespace\\CityController:universities");
    $this->get('/universities/{id}/centres', "$namespace\\UniversityController:centres");
    $this->get('/centres/{id}/degrees', "$namespace\\CentreController:degrees");
    $this->get('/degrees/{id}/subjects', "$namespace\\DegreeController:subjects");
});

$app->group('', function () use ($namespace) {
    $this->get('/guest', "$namespace\\LoginController:showWelcome");
    $this->get('/register', "$namespace\\UserController:showRegister");
    $this->post('/register', "$namespace\\UserController:register");
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

    // Recognizer
    $this->get('/users/register/recognizer', "$namespace\\UserController:showRegisterRecognizer");
    $this->post('/users/register/recognizer', "$namespace\\UserController:registerRecognizer");
    // Coordinator
    $this->get('/users/register/coordinator', "$namespace\\UserController:showRegisterCoordinator");
    $this->post('/users/register/coordinator', "$namespace\\UserController:registerCoordinator");

    $this->get('/search', "$namespace\\SearchController:results");
})->add($authenticate);
