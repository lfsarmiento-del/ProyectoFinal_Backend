<?php

use App\Controllers\AuthController;
use App\Helpers\ResponseHelper;
use App\Middleware\AuthMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/** @var \Slim\App $app */

$authController = new AuthController();

$app->get('/health', function (Request $request, Response $response) {
    return ResponseHelper::success($response, 'Microservicio de autenticacion funcionando correctamente', [
        'microservice' => 'ms-auth'
    ]);
});

$app->post('/login', [$authController, 'login']);

$app->post('/logout', [$authController, 'logout'])
    ->add(new AuthMiddleware());

$app->get('/validar-token', [$authController, 'validarToken'])
    ->add(new AuthMiddleware());