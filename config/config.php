<?php

declare(strict_types=1);

use App\Controllers\BaseController,
    DI\ContainerBuilder,
    Dotenv\Dotenv,
    Manju\ORM,
    Psr\Log\LoggerInterface,
    Slim\App;

(Dotenv::createUnsafeImmutable(dirname(__DIR__)))->load();

// Create the container for dependency injection.
$containerBuilder = new ContainerBuilder();
$containerBuilder->useAutowiring(true);
$containerBuilder->useAnnotations(true);
$containerBuilder->addDefinitions(__DIR__ . '/defaults/definitions.php');
if (file_exists(__DIR__ . '/definitions.php')) $containerBuilder->addDefinitions(__DIR__ . '/definitions.php');
$container = $containerBuilder->build();

$app = $container->get(App::class);

if (php_sapi_name() !== "cli") {
    session_set_cookie_params(["SameSite" => "Strict"]); //none, lax, strict
    session_set_cookie_params(["Secure" => "true"]); //false, true
    //session_set_cookie_params(["HttpOnly" => "true"]); //false, true
    session_start();
}
date_default_timezone_set($container->get('settings')->get('app.tz'));
ini_set('default_mimetype', '');

require_once __DIR__ . '/defaults/middlewares.php';
require_once __DIR__ . '/defaults/routes.php';
if (file_exists(__DIR__ . '/routes.php')) require_once __DIR_ . '/routes.php';


return $app;
