<?php

namespace App\Controllers;

use App\Helpers\ResponseHelper;
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

        return ResponseHelper::success($response, 'Listado de pedidos obtenido correctamente.', [
            'pedidos' => $pedidos->toArray()
        ]);
    }

    public function obtener(Request $request, Response $response, array $args): Response
    {
        $pedido = Pedido::with('detalles')->find($args['id']);

        if (!$pedido) {
            return ResponseHelper::error($response, 'Pedido no encontrado.', 404);
        }

        return ResponseHelper::success($response, 'Pedido encontrado correctamente.', [
            'pedido' => $pedido->toArray()
        ]);
    }

    public function crear(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $mesaId = (int) ($data['mesa_id'] ?? 0);
        $fecha = $data['fecha'] ?? date('Y-m-d');
        $hora = $data['hora'] ?? date('H:i:s');
        $estado = $data['estado'] ?? 'pendiente';

        $validacion = $this->validarPedido([
            'mesa_id' => $mesaId,
            'fecha' => $fecha,
            'hora' => $hora,
            'estado' => $estado
        ]);

        if ($validacion !== true) {
            return ResponseHelper::error($response, $validacion, 400);
        }

        $pedido = Pedido::create([
            'mesa_id' => $mesaId,
            'fecha' => $fecha,
            'hora' => $hora,
            'subtotal' => 0,
            'total' => 0,
            'estado' => $estado
        ]);

        return ResponseHelper::success($response, 'Pedido creado correctamente.', [
            'pedido' => $pedido->toArray()
        ]);
    }

    public function actualizar(Request $request, Response $response, array $args): Response
    {
        $pedido = Pedido::find($args['id']);

        if (!$pedido) {
            return ResponseHelper::error($response, 'Pedido no encontrado.', 404);
        }

        $data = $request->getParsedBody();

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

        if ($pedido->detalles()->count() > 0) {
            return ResponseHelper::error($response, 'No se puede eliminar el pedido porque tiene productos asociados.', 400);
        }

        $pedido->delete();

        return ResponseHelper::success($response, 'Pedido eliminado correctamente.');
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
}