<?php

require __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../app/Config/database.php';
require_once __DIR__ . '/../app/Models/Usuario.php';
require_once __DIR__ . '/../app/Helpers/ResponseHelper.php';
require_once __DIR__ . '/../app/Middleware/CorsMiddleware.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';

use Slim\Factory\AppFactory;
use App\Middleware\CorsMiddleware;

$app = AppFactory::create();

$app->add(new CorsMiddleware());
$app->addBodyParsingMiddleware();

require_once __DIR__ . '/../app/Routes/routes.php';

$app->run();