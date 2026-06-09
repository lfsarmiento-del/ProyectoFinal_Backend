<?php

use App\Controllers\CategoriaController;
use App\Helpers\ResponseHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$categoriaController = new CategoriaController();

$app->get('/health', function (Request $request, Response $response) {
    return ResponseHelper::success($response, 'Microservicio de productos funcionando correctamente', [
        'microservice' => 'ms-productos'
    ]);
});

$app->get('/categorias', [$categoriaController, 'listar']);
$app->get('/categorias/{id}', [$categoriaController, 'obtener']);
$app->post('/categorias', [$categoriaController, 'crear']);
$app->put('/categorias/{id}', [$categoriaController, 'actualizar']);
$app->delete('/categorias/{id}', [$categoriaController, 'eliminar']);