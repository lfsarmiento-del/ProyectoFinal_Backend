<?php

use App\Controllers\MesaController;
use App\Helpers\ResponseHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$mesaController = new MesaController();

$app->get('/health', function (Request $request, Response $response) {
    return ResponseHelper::success($response, 'Microservicio de reservas y mesas funcionando correctamente', [
        'microservice' => 'ms-reservas'
    ]);
});

$app->get('/mesas', [$mesaController, 'listar']);
$app->get('/mesas/{id}', [$mesaController, 'obtener']);
$app->post('/mesas', [$mesaController, 'crear']);
$app->put('/mesas/{id}', [$mesaController, 'actualizar']);
$app->patch('/mesas/{id}/estado', [$mesaController, 'cambiarEstado']);
$app->delete('/mesas/{id}', [$mesaController, 'eliminar']);