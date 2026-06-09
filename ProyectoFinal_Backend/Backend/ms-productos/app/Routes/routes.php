<?php

use App\Helpers\ResponseHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/health', function (Request $request, Response $response) {
    return ResponseHelper::success($response, 'Microservicio de productos funcionando correctamente', [
        'microservice' => 'ms-productos'
    ]);
});

$app->get('/categorias', function (Request $request, Response $response) {
    return ResponseHelper::success($response, 'Ruta para listar categorias preparada', [
        'endpoint' => '/categorias',
        'method' => 'GET'
    ]);
});

$app->get('/productos', function (Request $request, Response $response) {
    return ResponseHelper::success($response, 'Ruta para listar productos preparada', [
        'endpoint' => '/productos',
        'method' => 'GET'
    ]);
});

$app->post('/productos', function (Request $request, Response $response) {
    return ResponseHelper::success($response, 'Ruta para crear producto preparada', [
        'endpoint' => '/productos',
        'method' => 'POST'
    ]);
});