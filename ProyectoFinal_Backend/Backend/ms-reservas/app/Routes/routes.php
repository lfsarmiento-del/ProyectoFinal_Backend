<?php

use App\Helpers\ResponseHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/health', function (Request $request, Response $response) {
    return ResponseHelper::success($response, 'Microservicio de reservas y mesas funcionando correctamente', [
        'microservice' => 'ms-reservas'
    ]);
});

$app->get('/mesas', function (Request $request, Response $response) {
    return ResponseHelper::success($response, 'Ruta para listar mesas preparada', [
        'endpoint' => '/mesas',
        'method' => 'GET'
    ]);
});

$app->post('/mesas', function (Request $request, Response $response) {
    return ResponseHelper::success($response, 'Ruta para crear mesa preparada', [
        'endpoint' => '/mesas',
        'method' => 'POST'
    ]);
});

$app->get('/reservas', function (Request $request, Response $response) {
    return ResponseHelper::success($response, 'Ruta para listar reservas preparada', [
        'endpoint' => '/reservas',
        'method' => 'GET'
    ]);
});

$app->post('/reservas', function (Request $request, Response $response) {
    return ResponseHelper::success($response, 'Ruta para crear reserva preparada', [
        'endpoint' => '/reservas',
        'method' => 'POST'
    ]);
});