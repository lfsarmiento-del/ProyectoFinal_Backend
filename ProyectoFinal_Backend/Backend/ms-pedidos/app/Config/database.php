<?php

use Illuminate\Database\Capsule\Manager as Capsule;

$envPath = __DIR__ . '/../../.env';

if (!file_exists($envPath)) {
    throw new Exception('No se encontró el archivo .env');
}

$env = parse_ini_file($envPath);

$capsule = new Capsule();

$capsule->addConnection([
    'driver' => 'mysql',
    'host' => $env['DB_HOST'] ?? 'localhost',
    'database' => $env['DB_NAME'] ?? '',
    'username' => $env['DB_USER'] ?? 'root',
    'password' => $env['DB_PASS'] ?? '',
    'port' => $env['DB_PORT'] ?? 3306,
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();