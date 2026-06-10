<?php

use App\Controllers\CategoriaController;
use App\Controllers\ProductoController;
use App\Helpers\ResponseHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/** @var \Slim\App $app */

$categoriaController = new CategoriaController();
$productoController = new ProductoController();

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

$app->get('/productos', [$productoController, 'listar']);
$app->get('/productos/disponibles', [$productoController, 'listarDisponibles']);
$app->get('/productos/categoria/{categoria_id}', [$productoController, 'listarPorCategoria']);
$app->get('/productos/{id}', [$productoController, 'obtener']);
$app->post('/productos', [$productoController, 'crear']);
$app->put('/productos/{id}', [$productoController, 'actualizar']);
$app->delete('/productos/{id}', [$productoController, 'eliminar']);