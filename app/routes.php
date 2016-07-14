<?php

$namespace = '\App\Http\Controllers';

$app->group('', function () use ($namespace) {
    $this->get('/', "$namespace\\HomeController:hello");
    $this->get('/login', "$namespace\\HomeController:login");
    $this->get('/login/callback', "$namespace\\HomeController:loginCallback");
});
