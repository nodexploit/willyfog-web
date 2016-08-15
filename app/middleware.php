<?php

$app->add(new \App\Http\Middleware\SessionMiddleware(['name' => 'session']));
$guest = new \App\Http\Middleware\GuestMiddleware();
$authenticate = new \App\Http\Middleware\AuthenticateMiddleware();
