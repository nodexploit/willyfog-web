<?php

$container = $app->getContainer();

$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));

    return $logger;
};

$container['session'] = function ($c) {
    return new \App\Http\Session();
};

$container['flash'] = function ($c) {
    return new \Slim\Flash\Messages();
};

$container['auth'] = function ($c) {
    $auth = \App\Http\Auth::getInstance();
    $auth->setCI($c);

    return $auth;
};

$container['view'] = function ($c) {
    $settings = $c->get('settings')['renderer'];

    $view = new \Slim\Views\Twig($settings['template_path']);
    $view->addExtension(new \Slim\Views\TwigExtension(
        $c['router'],
        $c['request']->getUri()
    ));
    $view->offsetSet('flash', $c->get('flash'));
    $view->offsetSet('session', $c->get('session'));
    $view->offsetSet('auth', $c->get('auth'));

    return $view;
};
