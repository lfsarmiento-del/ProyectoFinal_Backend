<?php

use App\Controllers\PedidoController;
use App\Helpers\ResponseHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$pedidoController = new PedidoController();

$app->get('/health', function (Request $request, Response $response) {
    return ResponseHelper::success($response, 'Microservicio de pedidos funcionando correctamente', [
        'microservice' => 'ms-pedidos'
    ]);
});

$app->get('/pedidos', [$pedidoController, 'listar']);
$app->post('/pedidos', [$pedidoController, 'crear']);
$app->get('/pedidos/{id}', [$pedidoController, 'obtener']);
$app->put('/pedidos/{id}', [$pedidoController, 'actualizar']);
$app->patch('/pedidos/{id}/estado', [$pedidoController, 'cambiarEstado']);
$app->delete('/pedidos/{id}', [$pedidoController, 'eliminar']);