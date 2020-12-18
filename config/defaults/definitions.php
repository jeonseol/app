<?php

use Adbar\Dot,
    App\Middlewares\SlimErrorHandler,
    Manju\Connection;
use Monolog\{
    Handler\FilterHandler, Handler\StreamHandler, Logger, Processor\UidProcessor
};
use NGSOFT\Tools\Cache\PHPCache;
use Psr\{
    Cache\CacheItemPoolInterface, Container\ContainerInterface, Http\Message\ResponseFactoryInterface,
    Http\Message\ServerRequestInterface, Http\Server\RequestHandlerInterface, Log\LoggerInterface
};
use Slim\{
    App, Csrf\Guard, Factory\AppFactory, Interfaces\CallableResolverInterface, Interfaces\ErrorHandlerInterface,
    Interfaces\RouteParserInterface, Views\Twig
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
    CallableResolverInterface::class => function (ContainerInterface $container) {
        return $container->get(App::class)->getCallableResolver();
    },
    ErrorHandlerInterface::class => function(ContainerInterface $container) {

        return new SlimErrorHandler($container->get(CallableResolverInterface::class), $container->get(ResponseFactoryInterface::class), $container->get(LoggerInterface::class), $container);
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
        return new Logger($settings->get('app.title'), $handlers, [$processor]);
    },
    CacheItemPoolInterface::class => function (ContainerInterface $container) {
        $settings = $container->get('settings');
        return new PHPCache($settings->get('cache.path'), $settings->get('cache.ttl'), $settings->get('cache.namespace'));
    },
    Connection::class => function(ContainerInterface $container) {

        $settings = $container->get('settings');
        $host = getenv('dbhost') ?? $settings->get('db.host');
        $port = getenv('dbport') ?? $settings->get('db.port');
        $dbname = getenv('dbname') ?? $settings->get('db.host');
        $user = getenv('dbuser') ?? $settings->get('db.user');
        $password = getenv('dbpassword') ?? $settings->get('db.password');

        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4;port=%s', $host, $dbname, $port);
        return new Connection([
            'dsn' => $dsn,
            'user' => $user,
            'password' => $password
        ]);
    },
];
