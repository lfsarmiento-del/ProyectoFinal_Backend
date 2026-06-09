<?php

namespace App\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Usuario;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController
{
    public function login(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $usuarioInput = trim($data['usuario'] ?? '');
        $contrasena = trim($data['contrasena'] ?? '');

        if ($usuarioInput === '' || $contrasena === '') {
            return ResponseHelper::error($response, 'Usuario y contraseña son obligatorios.', 400);
        }

        $usuario = Usuario::where('usuario', $usuarioInput)
            ->orWhere('correo', $usuarioInput)
            ->first();

        if (!$usuario) {
            return ResponseHelper::error($response, 'Credenciales incorrectas.', 401);
        }

        if ($usuario->estado !== 'activo') {
            return ResponseHelper::error($response, 'El usuario se encuentra inactivo.', 403);
        }

        if ($usuario->contrasena !== $contrasena) {
            return ResponseHelper::error($response, 'Credenciales incorrectas.', 401);
        }

        $token = bin2hex(random_bytes(32));

        $usuario->token = $token;
        $usuario->sesion_activa = true;
        $usuario->save();

        return ResponseHelper::success($response, 'Inicio de sesión correcto.', [
            'token' => $token,
            'usuario' => [
                'id' => $usuario->id,
                'nombre' => $usuario->nombre,
                'correo' => $usuario->correo,
                'rol' => $usuario->rol
            ]
        ]);
    }

    public function logout(Request $request, Response $response): Response
    {
        $token = $this->obtenerToken($request);

        if (!$token) {
            return ResponseHelper::error($response, 'Token no enviado.', 401);
        }

        $usuario = Usuario::where('token', $token)->first();

        if (!$usuario) {
            return ResponseHelper::error($response, 'Sesión no encontrada.', 404);
        }

        $usuario->token = null;
        $usuario->sesion_activa = false;
        $usuario->save();

        return ResponseHelper::success($response, 'Sesión cerrada correctamente.');
    }

    public function validarToken(Request $request, Response $response): Response
    {
        $token = $this->obtenerToken($request);

        if (!$token) {
            return ResponseHelper::error($response, 'Token no enviado.', 401);
        }

        $usuario = Usuario::where('token', $token)
            ->where('sesion_activa', true)
            ->where('estado', 'activo')
            ->first();

        if (!$usuario) {
            return ResponseHelper::error($response, 'Token inválido o sesión inactiva.', 401);
        }

        return ResponseHelper::success($response, 'Token válido.', [
            'usuario' => [
                'id' => $usuario->id,
                'nombre' => $usuario->nombre,
                'correo' => $usuario->correo,
                'rol' => $usuario->rol
            ]
        ]);
    }

    private function obtenerToken(Request $request): ?string
    {
        $header = $request->getHeaderLine('Authorization');

        if (!$header) {
            return null;
        }

        return str_replace('Bearer ', '', $header);
    }
}