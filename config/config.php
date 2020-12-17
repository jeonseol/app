<?php

declare(strict_types=1);

use DI\ContainerBuilder,
    Dotenv\Dotenv;
use Slim\{
    App, Views\Twig
};

(Dotenv::createUnsafeImmutable(dirname(__DIR__)))->load();

// Create the container for dependency injection.
$containerBuilder = new ContainerBuilder();
$containerBuilder->useAutowiring(true);
$containerBuilder->useAnnotations(true);
$containerBuilder->addDefinitions(__DIR__ . '/defaults/definitions.php');
if (file_exists(__DIR__ . '/definitions.php')) $containerBuilder->addDefinitions(__DIR__ . '/definitions.php');
$container = $containerBuilder->build();

$app = $container->get(App::class);

require_once __DIR__ . '/defaults/middlewares.php';
require_once __DIR__ . '/defaults/routes.php';
if (file_exists(__DIR__ . '/routes.php')) require_once __DIR_ . '/routes.php';

return $app;
