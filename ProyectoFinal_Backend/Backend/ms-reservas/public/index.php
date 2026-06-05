<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$app->get('/health', function (Request $request, Response $response) {
    $data = [
        'status' => true,
        'microservice' => 'ms-reservas',
        'message' => 'Microservicio de reservas y mesas funcionando correctamente'
    ];

    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();