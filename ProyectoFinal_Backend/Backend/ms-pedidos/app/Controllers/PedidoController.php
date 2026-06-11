<?php

namespace App\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\DetallePedido;
use App\Models\Pedido;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PedidoController
{
    public function listar(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();

        $query = Pedido::query();

        if (!empty($params['estado'])) {
            $query->where('estado', $params['estado']);
        }

        if (!empty($params['mesa_id'])) {
            $query->where('mesa_id', $params['mesa_id']);
        }

        if (!empty($params['fecha'])) {
            $query->where('fecha', $params['fecha']);
        }

        $pedidos = $query->orderBy('id', 'desc')->get();

        return ResponseHelper::success($response, 'El listado de pedidos fue obtenido correctamente.', [
            'pedidos' => $pedidos->toArray()
        ]);
    }

    public function obtener(Request $request, Response $response, array $args): Response
    {
        $pedido = Pedido::with('detalles')->find($args['id']);

        if (!$pedido) {
            return ResponseHelper::error($response, 'Pedido no encontrado.', 404);
        }

        $cantidadTotal = $pedido->detalles()->sum('cantidad');

        return ResponseHelper::success($response, 'El Pedido fue encontrado correctamente.', [
            'pedido' => $pedido->toArray(),
            'cantidad_total_productos' => (int) $cantidadTotal
        ]);
    }

    public function crear(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $data = [];
        }

        $mesaId = (int) ($data['mesa_id'] ?? 0);
        $fecha = $data['fecha'] ?? date('Y-m-d');
        $hora = $data['hora'] ?? date('H:i:s');
        $estado = $data['estado'] ?? 'pendiente';
        $productos = $data['productos'] ?? [];

        $validacion = $this->validarPedido([
            'mesa_id' => $mesaId,
            'fecha' => $fecha,
            'hora' => $hora,
            'estado' => $estado
        ]);

        if ($validacion !== true) {
            return ResponseHelper::error($response, $validacion, 400);
        }

        if (!is_array($productos) || count($productos) === 0) {
            return ResponseHelper::error($response, 'El pedido debe tener al menos un producto.', 400);
        }

        $pedido = Pedido::create([
            'mesa_id' => $mesaId,
            'fecha' => $fecha,
            'hora' => $hora,
            'subtotal' => 0,
            'total' => 0,
            'estado' => $estado
        ]);

        foreach ($productos as $producto) {
            $validacionDetalle = $this->validarDetalle($producto);

            if ($validacionDetalle !== true) {
                $pedido->detalles()->delete();
                $pedido->delete();

                return ResponseHelper::error($response, $validacionDetalle, 400);
            }

            $cantidad = (int) $producto['cantidad'];
            $precioUnitario = (float) $producto['precio_unitario'];
            $subtotal = $cantidad * $precioUnitario;

            DetallePedido::create([
                'pedido_id' => $pedido->id,
                'producto_id' => (int) $producto['producto_id'],
                'nombre_producto' => trim($producto['nombre_producto']),
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnitario,
                'subtotal' => $subtotal
            ]);
        }

        $this->recalcularTotales($pedido);

        $pedido = Pedido::with('detalles')->find($pedido->id);

        return ResponseHelper::success($response, 'Pedido creado correctamente.', [
            'pedido' => $pedido->toArray(),
            'cantidad_total_productos' => (int) $pedido->detalles()->sum('cantidad')
        ]);
    }

    public function actualizar(Request $request, Response $response, array $args): Response
    {
        $pedido = Pedido::find($args['id']);

        if (!$pedido) {
            return ResponseHelper::error($response, 'Pedido no encontrado.', 404);
        }

        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $data = [];
        }

        $mesaId = (int) ($data['mesa_id'] ?? $pedido->mesa_id);
        $fecha = $data['fecha'] ?? $pedido->fecha;
        $hora = $data['hora'] ?? $pedido->hora;
        $estado = $data['estado'] ?? $pedido->estado;

        $validacion = $this->validarPedido([
            'mesa_id' => $mesaId,
            'fecha' => $fecha,
            'hora' => $hora,
            'estado' => $estado
        ]);

        if ($validacion !== true) {
            return ResponseHelper::error($response, $validacion, 400);
        }

        $pedido->mesa_id = $mesaId;
        $pedido->fecha = $fecha;
        $pedido->hora = $hora;
        $pedido->estado = $estado;
        $pedido->save();

        return ResponseHelper::success($response, 'Pedido actualizado correctamente.', [
            'pedido' => $pedido->toArray()
        ]);
    }

    public function cambiarEstado(Request $request, Response $response, array $args): Response
    {
        $pedido = Pedido::find($args['id']);

        if (!$pedido) {
            return ResponseHelper::error($response, 'Pedido no encontrado.', 404);
        }

        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $data = [];
        }

        $estado = $data['estado'] ?? '';

        $estadosPermitidos = [
            'pendiente',
            'en_preparacion',
            'entregado',
            'pagado',
            'cancelado'
        ];

        if (!in_array($estado, $estadosPermitidos)) {
            return ResponseHelper::error($response, 'Estado de pedido no válido.', 400);
        }

        $pedido->estado = $estado;
        $pedido->save();

        return ResponseHelper::success($response, 'Estado del pedido actualizado correctamente.', [
            'pedido' => $pedido->toArray()
        ]);
    }

    public function eliminar(Request $request, Response $response, array $args): Response
    {
        $pedido = Pedido::with('detalles')->find($args['id']);

        if (!$pedido) {
            return ResponseHelper::error($response, 'Pedido no encontrado.', 404);
        }

        $pedido->detalles()->delete();
        $pedido->delete();

        return ResponseHelper::success($response, 'Pedido eliminado correctamente.');
    }

    public function agregarDetalle(Request $request, Response $response, array $args): Response
    {
        $pedido = Pedido::find($args['id']);

        if (!$pedido) {
            return ResponseHelper::error($response, 'Pedido no encontrado.', 404);
        }

        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $data = [];
        }

        $validacion = $this->validarDetalle($data);

        if ($validacion !== true) {
            return ResponseHelper::error($response, $validacion, 400);
        }

        $cantidad = (int) $data['cantidad'];
        $precioUnitario = (float) $data['precio_unitario'];
        $subtotal = $cantidad * $precioUnitario;

        $detalle = DetallePedido::create([
            'pedido_id' => $pedido->id,
            'producto_id' => (int) $data['producto_id'],
            'nombre_producto' => trim($data['nombre_producto']),
            'cantidad' => $cantidad,
            'precio_unitario' => $precioUnitario,
            'subtotal' => $subtotal
        ]);

        $this->recalcularTotales($pedido);

        $pedido = Pedido::with('detalles')->find($pedido->id);

        return ResponseHelper::success($response, 'Producto agregado al pedido correctamente.', [
            'detalle' => $detalle->toArray(),
            'pedido' => $pedido->toArray(),
            'cantidad_total_productos' => (int) $pedido->detalles()->sum('cantidad')
        ]);
    }

    public function actualizarDetalle(Request $request, Response $response, array $args): Response
    {
        $pedido = Pedido::find($args['id']);

        if (!$pedido) {
            return ResponseHelper::error($response, 'Pedido no encontrado.', 404);
        }

        $detalle = DetallePedido::where('pedido_id', $pedido->id)
            ->where('id', $args['detalle_id'])
            ->first();

        if (!$detalle) {
            return ResponseHelper::error($response, 'Detalle de pedido no encontrado.', 404);
        }

        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $data = [];
        }

        $dataCompleta = [
            'producto_id' => $data['producto_id'] ?? $detalle->producto_id,
            'nombre_producto' => $data['nombre_producto'] ?? $detalle->nombre_producto,
            'cantidad' => $data['cantidad'] ?? $detalle->cantidad,
            'precio_unitario' => $data['precio_unitario'] ?? $detalle->precio_unitario
        ];

        $validacion = $this->validarDetalle($dataCompleta);

        if ($validacion !== true) {
            return ResponseHelper::error($response, $validacion, 400);
        }

        $cantidad = (int) $dataCompleta['cantidad'];
        $precioUnitario = (float) $dataCompleta['precio_unitario'];
        $subtotal = $cantidad * $precioUnitario;

        $detalle->producto_id = (int) $dataCompleta['producto_id'];
        $detalle->nombre_producto = trim($dataCompleta['nombre_producto']);
        $detalle->cantidad = $cantidad;
        $detalle->precio_unitario = $precioUnitario;
        $detalle->subtotal = $subtotal;
        $detalle->save();

        $this->recalcularTotales($pedido);

        $pedido = Pedido::with('detalles')->find($pedido->id);

        return ResponseHelper::success($response, 'Detalle del pedido actualizado correctamente.', [
            'detalle' => $detalle->toArray(),
            'pedido' => $pedido->toArray(),
            'cantidad_total_productos' => (int) $pedido->detalles()->sum('cantidad')
        ]);
    }

    public function eliminarDetalle(Request $request, Response $response, array $args): Response
    {
        $pedido = Pedido::find($args['id']);

        if (!$pedido) {
            return ResponseHelper::error($response, 'Pedido no encontrado.', 404);
        }

        $detalle = DetallePedido::where('pedido_id', $pedido->id)
            ->where('id', $args['detalle_id'])
            ->first();

        if (!$detalle) {
            return ResponseHelper::error($response, 'Detalle de pedido no encontrado.', 404);
        }

        if ($pedido->detalles()->count() <= 1) {
            return ResponseHelper::error($response, 'No se puede eliminar el último producto porque el pedido quedaría vacío.', 400);
        }

        $detalle->delete();

        $this->recalcularTotales($pedido);

        $pedido = Pedido::with('detalles')->find($pedido->id);

        return ResponseHelper::success($response, 'Producto eliminado del pedido correctamente.', [
            'pedido' => $pedido->toArray(),
            'cantidad_total_productos' => (int) $pedido->detalles()->sum('cantidad')
        ]);
    }

    private function validarPedido(array $data)
    {
        $mesaId = (int) ($data['mesa_id'] ?? 0);
        $fecha = $data['fecha'] ?? '';
        $hora = $data['hora'] ?? '';
        $estado = $data['estado'] ?? 'pendiente';

        $estadosPermitidos = [
            'pendiente',
            'en_preparacion',
            'entregado',
            'pagado',
            'cancelado'
        ];

        if ($mesaId <= 0) {
            return 'Debe seleccionar una mesa válida.';
        }

        if ($fecha === '') {
            return 'La fecha del pedido es obligatoria.';
        }

        if ($hora === '') {
            return 'La hora del pedido es obligatoria.';
        }

        if (!in_array($estado, $estadosPermitidos)) {
            return 'Estado de pedido no válido.';
        }

        return true;
    }

    private function validarDetalle(array $data)
    {
        $productoId = (int) ($data['producto_id'] ?? 0);
        $nombreProducto = trim($data['nombre_producto'] ?? '');
        $cantidad = (int) ($data['cantidad'] ?? 0);
        $precioUnitario = (float) ($data['precio_unitario'] ?? 0);

        if ($productoId <= 0) {
            return 'Debe seleccionar un producto válido.';
        }

        if ($nombreProducto === '') {
            return 'El nombre del producto es obligatorio.';
        }

        if ($cantidad < 1) {
            return 'La cantidad del producto debe ser mayor o igual a uno.';
        }

        if ($precioUnitario <= 0) {
            return 'El precio unitario del producto debe ser mayor a cero.';
        }

        return true;
    }

    private function recalcularTotales(Pedido $pedido): void
    {
        $subtotal = (float) $pedido->detalles()->sum('subtotal');

        $pedido->subtotal = $subtotal;
        $pedido->total = $subtotal;
        $pedido->save();
    }
}