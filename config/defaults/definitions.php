<?php

use Adbar\Dot;
use Monolog\{
    Handler\FilterHandler, Handler\StreamHandler, Logger, Processor\UidProcessor
};
use Psr\{
    Container\ContainerInterface, Http\Message\ResponseFactoryInterface, Http\Message\ServerRequestInterface,
    Http\Server\RequestHandlerInterface, Log\LoggerInterface
};
use Slim\{
    App, Csrf\Guard, Factory\AppFactory, Handlers\ErrorHandler, Interfaces\RouteParserInterface, Views\Twig
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
    ErrorHandler::class => function(ContainerInterface $container) {
        $app = $container->get(App::class);
        return new ErrorHandler($app->getCallableResolver(), $app->getResponseFactory(), $container->get(LoggerInterface::class));
    },
    Twig::class => function (ContainerInterface $container) {
        $settings = $container->get('settings');
        return Twig::create($settings ['twig.paths'], $settings ['twig.options']);
    },
    "view" => get(Twig::class),
    Guard::class => function (ContainerInterface $container) {
        $responseFactory = $container->get(App::class)->getResponseFactory();
        $guard = new Guard($responseFactory);

        $guard->setFailureHandler(function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
                    $request = $request->withAttribute("csrf_status", false);
                    return $handler->handle($request);
                });
        return $guard;
    },
    "csrf" => get(Guard::class),
    LoggerInterface::class => function(ContainerInterface $container) {

        $settings = $container->get('settings');
        $path = $settings->get('paths.logs');

        $handlers = [
            new FilterHandler(new StreamHandler($path . "/app.log", Logger::DEBUG), Logger::DEBUG, 399),
            new StreamHandler($path . "/error.log", Logger::ERROR),
        ];
        $processor = new UidProcessor();
        return new Logger('SlimApp', $handlers, [$processor]);
    },
];
