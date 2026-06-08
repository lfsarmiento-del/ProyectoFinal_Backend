<?php

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Middleware/CorsMiddleware.php';
require_once __DIR__ . '/../app/Helpers/ResponseHelper.php';

use Slim\Factory\AppFactory;
use App\Middleware\CorsMiddleware;
use App\Helpers\ResponseHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app = AppFactory::create();

$app->add(new CorsMiddleware());
$app->addBodyParsingMiddleware();

$app->get('/health', function (Request $request, Response $response) {
    return ResponseHelper::success($response, 'Microservicio de productos funcionando correctamente', [
        'microservice' => 'ms-productos'
    ]);
});

$app->run();