<?php

namespace App\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Mesa;
use App\Models\Reserva;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ReservaController
{
    public function listar(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();

        $query = Reserva::query();

        if (!empty($params['fecha'])) {
            $query->where('fecha', $params['fecha']);
        }

        if (!empty($params['cliente'])) {
            $query->where('nombre_cliente', 'LIKE', '%' . $params['cliente'] . '%');
        }

        if (!empty($params['estado'])) {
            $query->where('estado', $params['estado']);
        }

        $reservas = $query->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->get();

        return ResponseHelper::success($response, 'Listado de reservas obtenido correctamente.', [
            'reservas' => $reservas->toArray()
        ]);
    }

    public function obtener(Request $request, Response $response, array $args): Response
    {
        $reserva = Reserva::find($args['id']);

        if (!$reserva) {
            return ResponseHelper::error($response, 'Reserva no encontrada.', 404);
        }

        return ResponseHelper::success($response, 'Reserva encontrada correctamente.', [
            'reserva' => $reserva->toArray()
        ]);
    }

    public function crear(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $validacion = $this->validarDatosReserva($data);

        if ($validacion !== true) {
            return ResponseHelper::error($response, $validacion, 400);
        }

        $mesa = Mesa::find($data['mesa_id']);

        if (!$mesa) {
            return ResponseHelper::error($response, 'La mesa seleccionada no existe.', 404);
        }

        if ($mesa->estado === 'fuera_servicio') {
            return ResponseHelper::error($response, 'No se puede reservar una mesa fuera de servicio.', 400);
        }

        if ((int) $data['cantidad_personas'] > (int) $mesa->capacidad) {
            return ResponseHelper::error($response, 'La cantidad de personas supera la capacidad de la mesa.', 400);
        }

        $reservaExistente = Reserva::where('mesa_id', $data['mesa_id'])
            ->where('fecha', $data['fecha'])
            ->where('hora', $data['hora'])
            ->whereNotIn('estado', ['cancelada'])
            ->first();

        if ($reservaExistente) {
            return ResponseHelper::error($response, 'La mesa ya tiene una reserva para esa fecha y hora.', 409);
        }

        $reserva = Reserva::create([
            'nombre_cliente' => trim($data['nombre_cliente']),
            'telefono_cliente' => trim($data['telefono_cliente']),
            'cantidad_personas' => (int) $data['cantidad_personas'],
            'fecha' => $data['fecha'],
            'hora' => $data['hora'],
            'observaciones' => $data['observaciones'] ?? null,
            'estado' => $data['estado'] ?? 'pendiente',
            'mesa_id' => (int) $data['mesa_id']
        ]);

        $mesa->estado = 'reservada';
        $mesa->save();

        return ResponseHelper::success($response, 'Reserva creada correctamente.', [
            'reserva' => $reserva->toArray()
        ]);
    }

    public function actualizar(Request $request, Response $response, array $args): Response
    {
        $reserva = Reserva::find($args['id']);

        if (!$reserva) {
            return ResponseHelper::error($response, 'Reserva no encontrada.', 404);
        }

        $data = $request->getParsedBody();

        $dataCompleta = [
            'nombre_cliente' => $data['nombre_cliente'] ?? $reserva->nombre_cliente,
            'telefono_cliente' => $data['telefono_cliente'] ?? $reserva->telefono_cliente,
            'cantidad_personas' => $data['cantidad_personas'] ?? $reserva->cantidad_personas,
            'fecha' => $data['fecha'] ?? $reserva->fecha,
            'hora' => $data['hora'] ?? $reserva->hora,
            'observaciones' => $data['observaciones'] ?? $reserva->observaciones,
            'estado' => $data['estado'] ?? $reserva->estado,
            'mesa_id' => $data['mesa_id'] ?? $reserva->mesa_id
        ];

        $validacion = $this->validarDatosReserva($dataCompleta);

        if ($validacion !== true) {
            return ResponseHelper::error($response, $validacion, 400);
        }

        $mesa = Mesa::find($dataCompleta['mesa_id']);

        if (!$mesa) {
            return ResponseHelper::error($response, 'La mesa seleccionada no existe.', 404);
        }

        if ($mesa->estado === 'fuera_servicio') {
            return ResponseHelper::error($response, 'No se puede reservar una mesa fuera de servicio.', 400);
        }

        if ((int) $dataCompleta['cantidad_personas'] > (int) $mesa->capacidad) {
            return ResponseHelper::error($response, 'La cantidad de personas supera la capacidad de la mesa.', 400);
        }

        $reservaExistente = Reserva::where('mesa_id', $dataCompleta['mesa_id'])
            ->where('fecha', $dataCompleta['fecha'])
            ->where('hora', $dataCompleta['hora'])
            ->where('id', '!=', $reserva->id)
            ->whereNotIn('estado', ['cancelada'])
            ->first();

        if ($reservaExistente) {
            return ResponseHelper::error($response, 'La mesa ya tiene una reserva para esa fecha y hora.', 409);
        }

        $reserva->nombre_cliente = trim($dataCompleta['nombre_cliente']);
        $reserva->telefono_cliente = trim($dataCompleta['telefono_cliente']);
        $reserva->cantidad_personas = (int) $dataCompleta['cantidad_personas'];
        $reserva->fecha = $dataCompleta['fecha'];
        $reserva->hora = $dataCompleta['hora'];
        $reserva->observaciones = $dataCompleta['observaciones'];
        $reserva->estado = $dataCompleta['estado'];
        $reserva->mesa_id = (int) $dataCompleta['mesa_id'];
        $reserva->save();

        return ResponseHelper::success($response, 'Reserva actualizada correctamente.', [
            'reserva' => $reserva->toArray()
        ]);
    }

    public function cancelar(Request $request, Response $response, array $args): Response
    {
        $reserva = Reserva::find($args['id']);

        if (!$reserva) {
            return ResponseHelper::error($response, 'Reserva no encontrada.', 404);
        }

        $reserva->estado = 'cancelada';
        $reserva->save();

        return ResponseHelper::success($response, 'Reserva cancelada correctamente.', [
            'reserva' => $reserva->toArray()
        ]);
    }

    public function cambiarEstado(Request $request, Response $response, array $args): Response
    {
        $reserva = Reserva::find($args['id']);

        if (!$reserva) {
            return ResponseHelper::error($response, 'Reserva no encontrada.', 404);
        }

        $data = $request->getParsedBody();
        $estado = $data['estado'] ?? '';

        $estadosPermitidos = [
            'pendiente',
            'confirmada',
            'cancelada',
            'finalizada'
        ];

        if (!in_array($estado, $estadosPermitidos)) {
            return ResponseHelper::error($response, 'Estado de reserva no válido.', 400);
        }

        $reserva->estado = $estado;
        $reserva->save();

        return ResponseHelper::success($response, 'Estado de reserva actualizado correctamente.', [
            'reserva' => $reserva->toArray()
        ]);
    }

    private function validarDatosReserva(array $data)
    {
        $nombreCliente = trim($data['nombre_cliente'] ?? '');
        $telefonoCliente = trim($data['telefono_cliente'] ?? '');
        $cantidadPersonas = (int) ($data['cantidad_personas'] ?? 0);
        $fecha = $data['fecha'] ?? '';
        $hora = $data['hora'] ?? '';
        $mesaId = (int) ($data['mesa_id'] ?? 0);
        $estado = $data['estado'] ?? 'pendiente';

        $estadosPermitidos = [
            'pendiente',
            'confirmada',
            'cancelada',
            'finalizada'
        ];

        if ($nombreCliente === '') {
            return 'El nombre del cliente es obligatorio.';
        }

        if ($telefonoCliente === '') {
            return 'El teléfono del cliente es obligatorio.';
        }

        if ($cantidadPersonas <= 0) {
            return 'La cantidad de personas debe ser mayor a cero.';
        }

        if ($fecha === '') {
            return 'La fecha de la reserva es obligatoria.';
        }

        if ($fecha < date('Y-m-d')) {
            return 'No se permiten reservas en fechas pasadas.';
        }

        if ($hora === '') {
            return 'La hora de la reserva es obligatoria.';
        }

        if ($mesaId <= 0) {
            return 'Debe seleccionar una mesa válida.';
        }

        if (!in_array($estado, $estadosPermitidos)) {
            return 'Estado de reserva no válido.';
        }

        return true;
    }
}