<?php

namespace App\Middleware;

use App\Helpers\ResponseHelper;
use App\Models\Usuario;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class AuthMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): SlimResponse
    {
        $authorizationHeader = $request->getHeaderLine('Authorization');

        if (!$authorizationHeader) {
            $response = new SlimResponse();
            return ResponseHelper::error($response, 'Token no enviado.', 401);
        }

        if (!str_starts_with($authorizationHeader, 'Bearer ')) {
            $response = new SlimResponse();
            return ResponseHelper::error($response, 'Formato de token inválido.', 401);
        }

        $token = trim(str_replace('Bearer ', '', $authorizationHeader));

        if ($token === '') {
            $response = new SlimResponse();
            return ResponseHelper::error($response, 'Token vacío.', 401);
        }

        $usuario = Usuario::where('token', $token)
            ->where('sesion_activa', true)
            ->where('estado', 'activo')
            ->first();

        if (!$usuario) {
            $response = new SlimResponse();
            return ResponseHelper::error($response, 'Token inválido o sesión inactiva.', 401);
        }

        $request = $request->withAttribute('usuario_autenticado', $usuario);

        return $handler->handle($request);
    }
}