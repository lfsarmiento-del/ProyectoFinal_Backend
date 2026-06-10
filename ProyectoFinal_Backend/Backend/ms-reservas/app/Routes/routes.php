<?php

use App\Controllers\MesaController;
use App\Controllers\ReservaController;
use App\Helpers\ResponseHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/** @var \Slim\App $app */

$mesaController = new MesaController();
$reservaController = new ReservaController();

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

$app->get('/reservas', [$reservaController, 'listar']);
$app->get('/reservas/{id}', [$reservaController, 'obtener']);
$app->post('/reservas', [$reservaController, 'crear']);
$app->put('/reservas/{id}', [$reservaController, 'actualizar']);
$app->patch('/reservas/{id}/cancelar', [$reservaController, 'cancelar']);
$app->patch('/reservas/{id}/estado', [$reservaController, 'cambiarEstado']);