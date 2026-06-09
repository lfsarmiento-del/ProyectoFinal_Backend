<?php

namespace App\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Mesa;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class MesaController
{
    public function listar(Request $request, Response $response): Response
    {
        $mesas = Mesa::orderBy('id', 'asc')->get();

        return ResponseHelper::success($response, 'Listado de mesas obtenido correctamente.', [
            'mesas' => $mesas->toArray()
        ]);
    }

    public function obtener(Request $request, Response $response, array $args): Response
    {
        $mesa = Mesa::find($args['id']);

        if (!$mesa) {
            return ResponseHelper::error($response, 'Mesa no encontrada.', 404);
        }

        return ResponseHelper::success($response, 'Mesa encontrada correctamente.', [
            'mesa' => $mesa->toArray()
        ]);
    }

    public function crear(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $numero = trim($data['numero'] ?? '');
        $capacidad = (int) ($data['capacidad'] ?? 0);
        $estado = $data['estado'] ?? 'disponible';

        $estadosPermitidos = [
            'disponible',
            'reservada',
            'ocupada',
            'fuera_servicio'
        ];

        if ($numero === '') {
            return ResponseHelper::error($response, 'El número o nombre de la mesa es obligatorio.', 400);
        }

        if ($capacidad <= 0) {
            return ResponseHelper::error($response, 'La capacidad debe ser mayor a cero.', 400);
        }

        if (!in_array($estado, $estadosPermitidos)) {
            return ResponseHelper::error($response, 'Estado de mesa no válido.', 400);
        }

        $mesaExistente = Mesa::where('numero', $numero)->first();

        if ($mesaExistente) {
            return ResponseHelper::error($response, 'Ya existe una mesa con ese número o nombre.', 409);
        }

        $mesa = Mesa::create([
            'numero' => $numero,
            'capacidad' => $capacidad,
            'estado' => $estado
        ]);

        return ResponseHelper::success($response, 'Mesa creada correctamente.', [
            'mesa' => $mesa->toArray()
        ]);
    }

    public function actualizar(Request $request, Response $response, array $args): Response
    {
        $mesa = Mesa::find($args['id']);

        if (!$mesa) {
            return ResponseHelper::error($response, 'Mesa no encontrada.', 404);
        }

        $data = $request->getParsedBody();

        $numero = trim($data['numero'] ?? $mesa->numero);
        $capacidad = (int) ($data['capacidad'] ?? $mesa->capacidad);
        $estado = $data['estado'] ?? $mesa->estado;

        $estadosPermitidos = [
            'disponible',
            'reservada',
            'ocupada',
            'fuera_servicio'
        ];

        if ($numero === '') {
            return ResponseHelper::error($response, 'El número o nombre de la mesa es obligatorio.', 400);
        }

        if ($capacidad <= 0) {
            return ResponseHelper::error($response, 'La capacidad debe ser mayor a cero.', 400);
        }

        if (!in_array($estado, $estadosPermitidos)) {
            return ResponseHelper::error($response, 'Estado de mesa no válido.', 400);
        }

        $mesaDuplicada = Mesa::where('numero', $numero)
            ->where('id', '!=', $mesa->id)
            ->first();

        if ($mesaDuplicada) {
            return ResponseHelper::error($response, 'Ya existe otra mesa con ese número o nombre.', 409);
        }

        $mesa->numero = $numero;
        $mesa->capacidad = $capacidad;
        $mesa->estado = $estado;
        $mesa->save();

        return ResponseHelper::success($response, 'Mesa actualizada correctamente.', [
            'mesa' => $mesa->toArray()
        ]);
    }

    public function cambiarEstado(Request $request, Response $response, array $args): Response
    {
        $mesa = Mesa::find($args['id']);

        if (!$mesa) {
            return ResponseHelper::error($response, 'Mesa no encontrada.', 404);
        }

        $data = $request->getParsedBody();
        $estado = $data['estado'] ?? '';

        $estadosPermitidos = [
            'disponible',
            'reservada',
            'ocupada',
            'fuera_servicio'
        ];

        if (!in_array($estado, $estadosPermitidos)) {
            return ResponseHelper::error($response, 'Estado de mesa no válido.', 400);
        }

        $mesa->estado = $estado;
        $mesa->save();

        return ResponseHelper::success($response, 'Estado de mesa actualizado correctamente.', [
            'mesa' => $mesa->toArray()
        ]);
    }

    public function eliminar(Request $request, Response $response, array $args): Response
    {
        $mesa = Mesa::find($args['id']);

        if (!$mesa) {
            return ResponseHelper::error($response, 'Mesa no encontrada.', 404);
        }

        $mesa->delete();

        return ResponseHelper::success($response, 'Mesa eliminada correctamente.');
    }
}