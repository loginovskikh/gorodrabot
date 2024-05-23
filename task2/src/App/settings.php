<?php

use DI\ContainerBuilder;

return function(ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions(['settings' => [
        'db' => [
            'dbName' => getenv('DB_NAME'),
            'dbUser' => getenv('DB_USER'),
            'dbPassword' => getenv('DB_PASSWORD'),
            'dbHost' => getenv('DB_HOST'),
            'port' => getenv('DB_PORT')
        ],
        'displayErrorDetails' => true, //change to TRUE in production env
        'yandexApiKey' => 'cf035621-236d-4e2a-88a6-f21801ee31df',
        'yandexApiHost' => 'https://geocode-maps.yandex.ru/1.x/'
    ]]);
};
