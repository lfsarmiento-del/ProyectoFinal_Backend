<?php

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Middleware/CorsMiddleware.php';

use Slim\Factory\AppFactory;
use App\Middleware\CorsMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app = AppFactory::create();

$app->add(new CorsMiddleware());
$app->addBodyParsingMiddleware();

$app->get('/health', function (Request $request, Response $response) {
    $data = [
        'status' => true,
        'microservice' => 'ms-auth',
        'message' => 'Microservicio de autenticacion funcionando correctamente'
    ];

    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();