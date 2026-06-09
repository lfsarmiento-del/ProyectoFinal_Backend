<?php

use App\Helpers\ResponseHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/health', function (Request $request, Response $response) {
    return ResponseHelper::success($response, 'Microservicio de autenticacion funcionando correctamente', [
        'microservice' => 'ms-auth'
    ]);
});

$app->post('/login', function (Request $request, Response $response) {
    return ResponseHelper::success($response, 'Ruta de login preparada para implementación', [
        'endpoint' => '/login',
        'method' => 'POST'
    ]);
});

$app->post('/logout', function (Request $request, Response $response) {
    return ResponseHelper::success($response, 'Ruta de logout preparada para implementación', [
        'endpoint' => '/logout',
        'method' => 'POST'
    ]);
});