<?php
// Application middleware

$app->add(new \App\Http\Middleware\SessionMiddleware(['name' => 'session']));
// e.g: $app->add(new \Slim\Csrf\Guard);
