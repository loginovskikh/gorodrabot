<?php

use AddressFinder\ErrorRenderer\JsonErrorRenderer;
use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use Slim\Error\Renderers\HtmlErrorRenderer;
use Slim\Factory\ServerRequestCreatorFactory;

session_start();
require __DIR__ . '/../vendor/autoload.php';
$containerBuilder = new ContainerBuilder();

$settings = require __DIR__ . '/../src/App/settings.php';
$settings($containerBuilder);

$dependencies = require __DIR__ . '/../src/App/dependencies.php';
$dependencies($containerBuilder);

$container = $containerBuilder->build();

$app = Bridge::create($container);

$routes = require __DIR__ . '/../src/App/routes.php';

$routes($app);

$app->addRoutingMiddleware();

$displayErrorDetails = $container->get('settings')['displayErrorDetails'];
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, false, false);
$errorHandler = $errorMiddleware->getDefaultErrorHandler();

$errorHandler->registerErrorRenderer('text/html', HtmlErrorRenderer::class);
$errorHandler->registerErrorRenderer('application/json', JsonErrorRenderer::class);
$errorMiddleware->setDefaultErrorHandler($errorHandler);

$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

$app->run($request);
