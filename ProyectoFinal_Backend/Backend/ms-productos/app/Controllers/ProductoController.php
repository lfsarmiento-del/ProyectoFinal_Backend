<?php

namespace App\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Categoria;
use App\Models\Producto;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProductoController
{
    public function listar(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();

        $query = Producto::query();

        if (!empty($params['categoria_id'])) {
            $query->where('categoria_id', $params['categoria_id']);
        }

        if (isset($params['disponible'])) {
            $query->where('disponible', $this->convertirBooleano($params['disponible']));
        }

        $productos = $query->orderBy('id', 'asc')->get();

        return ResponseHelper::success($response, 'Listado de productos obtenido correctamente.', [
            'productos' => $productos->toArray()
        ]);
    }

    public function obtener(Request $request, Response $response, array $args): Response
    {
        $producto = Producto::find($args['id']);

        if (!$producto) {
            return ResponseHelper::error($response, 'Producto no encontrado.', 404);
        }

        return ResponseHelper::success($response, 'Producto encontrado correctamente.', [
            'producto' => $producto->toArray()
        ]);
    }

    public function crear(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $validacion = $this->validarProducto($data);

        if ($validacion !== true) {
            return ResponseHelper::error($response, $validacion, 400);
        }

        $categoria = Categoria::find($data['categoria_id']);

        if (!$categoria) {
            return ResponseHelper::error($response, 'La categoría seleccionada no existe.', 404);
        }

        $productoExistente = Producto::where('nombre', trim($data['nombre']))->first();

        if ($productoExistente) {
            return ResponseHelper::error($response, 'Ya existe un producto con ese nombre.', 409);
        }

        $producto = Producto::create([
            'nombre' => trim($data['nombre']),
            'descripcion' => $data['descripcion'] ?? null,
            'precio' => (float) $data['precio'],
            'disponible' => isset($data['disponible']) ? $this->convertirBooleano($data['disponible']) : true,
            'categoria_id' => (int) $data['categoria_id']
        ]);

        return ResponseHelper::success($response, 'Producto creado correctamente.', [
            'producto' => $producto->toArray()
        ]);
    }

    public function actualizar(Request $request, Response $response, array $args): Response
    {
        $producto = Producto::find($args['id']);

        if (!$producto) {
            return ResponseHelper::error($response, 'Producto no encontrado.', 404);
        }

        $data = $request->getParsedBody();

        $dataCompleta = [
            'nombre' => $data['nombre'] ?? $producto->nombre,
            'descripcion' => $data['descripcion'] ?? $producto->descripcion,
            'precio' => $data['precio'] ?? $producto->precio,
            'disponible' => $data['disponible'] ?? $producto->disponible,
            'categoria_id' => $data['categoria_id'] ?? $producto->categoria_id
        ];

        $validacion = $this->validarProducto($dataCompleta);

        if ($validacion !== true) {
            return ResponseHelper::error($response, $validacion, 400);
        }

        $categoria = Categoria::find($dataCompleta['categoria_id']);

        if (!$categoria) {
            return ResponseHelper::error($response, 'La categoría seleccionada no existe.', 404);
        }

        $productoDuplicado = Producto::where('nombre', trim($dataCompleta['nombre']))
            ->where('id', '!=', $producto->id)
            ->first();

        if ($productoDuplicado) {
            return ResponseHelper::error($response, 'Ya existe otro producto con ese nombre.', 409);
        }

        $producto->nombre = trim($dataCompleta['nombre']);
        $producto->descripcion = $dataCompleta['descripcion'];
        $producto->precio = (float) $dataCompleta['precio'];
        $producto->disponible = $this->convertirBooleano($dataCompleta['disponible']);
        $producto->categoria_id = (int) $dataCompleta['categoria_id'];
        $producto->save();

        return ResponseHelper::success($response, 'Producto actualizado correctamente.', [
            'producto' => $producto->toArray()
        ]);
    }

    public function eliminar(Request $request, Response $response, array $args): Response
    {
        $producto = Producto::find($args['id']);

        if (!$producto) {
            return ResponseHelper::error($response, 'Producto no encontrado.', 404);
        }

        $producto->delete();

        return ResponseHelper::success($response, 'Producto eliminado correctamente.');
    }

    public function listarDisponibles(Request $request, Response $response): Response
    {
        $productos = Producto::where('disponible', true)
            ->orderBy('id', 'asc')
            ->get();

        return ResponseHelper::success($response, 'Productos disponibles obtenidos correctamente.', [
            'productos' => $productos->toArray()
        ]);
    }

    public function listarPorCategoria(Request $request, Response $response, array $args): Response
    {
        $categoria = Categoria::find($args['categoria_id']);

        if (!$categoria) {
            return ResponseHelper::error($response, 'Categoría no encontrada.', 404);
        }

        $productos = Producto::where('categoria_id', $args['categoria_id'])
            ->orderBy('id', 'asc')
            ->get();

        return ResponseHelper::success($response, 'Productos por categoría obtenidos correctamente.', [
            'categoria' => $categoria->toArray(),
            'productos' => $productos->toArray()
        ]);
    }

    private function validarProducto(array $data)
    {
        $nombre = trim($data['nombre'] ?? '');
        $precio = (float) ($data['precio'] ?? 0);
        $categoriaId = (int) ($data['categoria_id'] ?? 0);

        if ($nombre === '') {
            return 'El nombre del producto es obligatorio.';
        }

        if ($precio <= 0) {
            return 'El precio debe ser mayor a cero.';
        }

        if ($categoriaId <= 0) {
            return 'Debe seleccionar una categoría válida.';
        }

        return true;
    }

    private function convertirBooleano($valor): bool
    {
        if (is_bool($valor)) {
            return $valor;
        }

        return in_array(strtolower((string) $valor), ['1', 'true', 'si', 'sí', 'activo', 'disponible']);
    }
}