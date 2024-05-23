<?php

use AddressFinder\Itegrations\AddressDataProvider\AddressDataProviderInterface;
use AddressFinder\Itegrations\AddressDataProvider\YandexDataProvider;
use AddressFinder\Repository\DB;
use DI\ContainerBuilder;
use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Slim\Views\Twig;

return function(ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        \PDO::class => function (ContainerInterface $c){
            return DB::getPDO($c->get('settings')['db']);
        },
        Twig::class => function(ContainerInterface $c) {
            return Twig::create(__DIR__ . '/../templates/', [
                'cache' => false,
                'debug' => true
            ]);
        },
        ClientInterface::class => function (ContainerInterface $c){
            return new Client(['http_errors' => false]);
        },

        AddressDataProviderInterface::class => function (ContainerInterface $c){
            return new YandexDataProvider(
                $c->get(ClientInterface::class),
                $c->get('settings')['yandexApiHost'],
                $c->get('settings')['yandexApiKey'],
            );
        },

    ]);
};
