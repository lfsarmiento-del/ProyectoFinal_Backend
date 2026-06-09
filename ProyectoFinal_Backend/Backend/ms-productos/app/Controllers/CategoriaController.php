<?php

namespace App\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Categoria;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CategoriaController
{
    public function listar(Request $request, Response $response): Response
    {
        $categorias = Categoria::orderBy('id', 'asc')->get();

        return ResponseHelper::success($response, 'Listado de categorías obtenido correctamente.', [
            'categorias' => $categorias->toArray()
        ]);
    }

    public function obtener(Request $request, Response $response, array $args): Response
    {
        $categoria = Categoria::find($args['id']);

        if (!$categoria) {
            return ResponseHelper::error($response, 'Categoría no encontrada.', 404);
        }

        return ResponseHelper::success($response, 'Categoría encontrada correctamente.', [
            'categoria' => $categoria->toArray()
        ]);
    }

    public function crear(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $nombre = trim($data['nombre'] ?? '');
        $descripcion = $data['descripcion'] ?? null;

        if ($nombre === '') {
            return ResponseHelper::error($response, 'El nombre de la categoría es obligatorio.', 400);
        }

        $categoriaExistente = Categoria::where('nombre', $nombre)->first();

        if ($categoriaExistente) {
            return ResponseHelper::error($response, 'Ya existe una categoría con ese nombre.', 409);
        }

        $categoria = Categoria::create([
            'nombre' => $nombre,
            'descripcion' => $descripcion
        ]);

        return ResponseHelper::success($response, 'Categoría creada correctamente.', [
            'categoria' => $categoria->toArray()
        ]);
    }

    public function actualizar(Request $request, Response $response, array $args): Response
    {
        $categoria = Categoria::find($args['id']);

        if (!$categoria) {
            return ResponseHelper::error($response, 'Categoría no encontrada.', 404);
        }

        $data = $request->getParsedBody();

        $nombre = trim($data['nombre'] ?? $categoria->nombre);
        $descripcion = $data['descripcion'] ?? $categoria->descripcion;

        if ($nombre === '') {
            return ResponseHelper::error($response, 'El nombre de la categoría es obligatorio.', 400);
        }

        $categoriaDuplicada = Categoria::where('nombre', $nombre)
            ->where('id', '!=', $categoria->id)
            ->first();

        if ($categoriaDuplicada) {
            return ResponseHelper::error($response, 'Ya existe otra categoría con ese nombre.', 409);
        }

        $categoria->nombre = $nombre;
        $categoria->descripcion = $descripcion;
        $categoria->save();

        return ResponseHelper::success($response, 'Categoría actualizada correctamente.', [
            'categoria' => $categoria->toArray()
        ]);
    }

    public function eliminar(Request $request, Response $response, array $args): Response
    {
        $categoria = Categoria::find($args['id']);

        if (!$categoria) {
            return ResponseHelper::error($response, 'Categoría no encontrada.', 404);
        }

        if ($categoria->productos()->count() > 0) {
            return ResponseHelper::error($response, 'No se puede eliminar la categoría porque tiene productos asociados.', 400);
        }

        $categoria->delete();

        return ResponseHelper::success($response, 'Categoría eliminada correctamente.');
    }
}