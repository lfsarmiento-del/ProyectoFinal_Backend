<?php

namespace App\Helpers;

use Psr\Http\Message\ResponseInterface as Response;

class ResponseHelper
{
    public static function json(Response $response, array $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }

    public static function success(Response $response, string $message, array $data = []): Response
    {
        return self::json($response, [
            'status' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    public static function error(Response $response, string $message, int $status = 400): Response
    {
        return self::json($response, [
            'status' => false,
            'message' => $message
        ], $status);
    }
}