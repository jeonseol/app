<?php

use Adbar\Dot,
    App\Middlewares\SlimErrorHandler,
    Manju\Connection;
use Monolog\{
    Handler\FilterHandler, Handler\StreamHandler, Logger, Processor\UidProcessor
};
use NGSOFT\{
    Commands\CommandMiddleware, Tools\Cache\PHPCache, Tools\Objects\stdObject
};
use Psr\{
    Cache\CacheItemPoolInterface, Container\ContainerInterface, Http\Message\ResponseFactoryInterface,
    Http\Message\ServerRequestInterface, Http\Server\RequestHandlerInterface, Log\LoggerInterface
};
use Selective\BasePath\BasePathMiddleware;
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
    "globals" => function() {
        return stdObject::create();
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
    BasePathMiddleware::class => function (ContainerInterface $container) {
        $app = $container->get(App::class);
        $sapi = php_sapi_name();
        return new BasePathMiddleware($app, $sapi);
    },
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
        $name = $settings->get('db.name');
        $host = $settings->get('db.host');
        $port = $settings->get('db.port');
        $dbname = $settings->get('db.dbname');
        $user = $settings->get('db.username');
        $password = $settings->get('db.password');
        $charset = $settings->get('db.charset');

        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s;port=%s', $host, $dbname, $charset, $port);

        return new Connection([
            'name' => $name,
            'dsn' => $dsn,
            'username' => $user,
            'password' => $password
        ]);
    },
    CommandMiddleware::class => function(ContainerInterface $container) {
        $middleware = new CommandMiddleware($container, $container->get(ResponseFactoryInterface::class));
        $middleware->displayTraceOnError($container->get('settings')->get('slim.displayerrordetails'));
        return $middleware;
    }
];
