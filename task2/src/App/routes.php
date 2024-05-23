<?php

use AddressFinder\Controller\Controller;
use AddressFinder\Middleware\UserIdMiddleware;
use Slim\App;

return function (App $app) {
    $app->get('/', [Controller::class, 'showForm']);
    $app->post('/', [Controller::class, 'search'])->add(new UserIdMiddleware());
};
