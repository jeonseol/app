<?php

use Adbar\Dot;
use Psr\{
    Container\ContainerInterface, Http\Message\ResponseFactoryInterface
};
use Slim\{
    App, Factory\AppFactory, Interfaces\RouteParserInterface, Views\Twig
};
use function DI\get;

return [
    //cli commands
    "commands" => [],
    "settings" => function() {
        $settings = require dirname(__DIR__) . '/settings.php';
        return new Dot($settings);
    },
    App::class => function (ContainerInterface $container) {
        AppFactory::setContainer($container);

        return AppFactory::create();
    },
    ResponseFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(App::class)->getResponseFactory();
    },
    RouteParserInterface::class => function (ContainerInterface $container) {
        return $container->get(App::class)->getRouteCollector()->getRouteParser();
    },
    Twig::class => function (ContainerInterface $container) {
        $settings = $container->get('settings');
        return Twig::create($settings ['twig.paths'], $settings ['twig.options']);
    },
    "view" => get(Twig::class),
    "csrf" => function(ContainerInterface $container) {
        $responseFactory = $container->get(App::class)->getResponseFactory();
        return new Guard($responseFactory);
    }
];
