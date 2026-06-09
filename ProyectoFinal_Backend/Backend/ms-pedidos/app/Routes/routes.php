<?php

use App\Helpers\ResponseHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/health', function (Request $request, Response $response) {
    return ResponseHelper::success($response, 'Microservicio de pedidos funcionando correctamente', [
        'microservice' => 'ms-pedidos'
    ]);
});

$app->get('/pedidos', function (Request $request, Response $response) {
    return ResponseHelper::success($response, 'Ruta para listar pedidos preparada', [
        'endpoint' => '/pedidos',
        'method' => 'GET'
    ]);
});

$app->post('/pedidos', function (Request $request, Response $response) {
    return ResponseHelper::success($response, 'Ruta para crear pedido preparada', [
        'endpoint' => '/pedidos',
        'method' => 'POST'
    ]);
});

$app->patch('/pedidos/{id}/estado', function (Request $request, Response $response, array $args) {
    return ResponseHelper::success($response, 'Ruta para cambiar estado del pedido preparada', [
        'endpoint' => '/pedidos/{id}/estado',
        'method' => 'PATCH',
        'pedido_id' => $args['id']
    ]);
});