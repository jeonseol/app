<?php

use Adbar\Dot,
    App\Middlewares\SlimErrorHandler,
    DI\Bridge\Slim\ControllerInvoker;
use Invoker\{
    Invoker, ParameterResolver\AssociativeArrayResolver, ParameterResolver\Container\TypeHintContainerResolver,
    ParameterResolver\DefaultValueResolver, ParameterResolver\ResolverChain
};
use Manju\Connection;
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
    Interfaces\RouteParserInterface, Views\Twig, Views\TwigMiddleware
};
use Twig\Extension\ExtensionInterface;
use function DI\get;

return [
    //cli commands
    "commands" => [],
    //site settings
    "settings" => function() {
        $settings = require dirname(__DIR__) . '/settings.php';
        return new Dot($settings);
    },
    //globals set on all Controllers extending BaseController
    "globals" => function(ContainerInterface $container) {
        $globals = (require dirname(__DIR__) . '/globals.php')($container);
        if (!is_array($globals)) $globals = [];
        return stdObject::from($globals);
    },
    //twig extensions
    "extensions" => function(ContainerInterface $container) {
        $extensions = (require dirname(__DIR__) . '/extensions.php')($container);
        $result = [];
        if (is_array($extensions)) {
            foreach ($extensions as $extension) {

                if (
                        is_object($extension)
                        and ($extension instanceof ExtensionInterface)
                ) {
                    $result[] = $extension;
                } elseif (
                        is_string($extension)
                        and class_exists($extension)
                        and in_array(ExtensionInterface::class, class_implements($extension))
                ) {
                    $result[] = $container->get($extension);
                }
            }
        }
        return $result;
    },
    App::class => function (ContainerInterface $container) {

        //php-di bridge breaks middlewares, so manually add the ControllerInvoker
        AppFactory::setContainer($container);
        $app = AppFactory::create();

        $invoker = new Invoker(
                new ResolverChain([
                    // Inject parameters by name first
                    new AssociativeArrayResolver(),
                    // Then inject services by type-hints for those that weren't resolved
                    new TypeHintContainerResolver($container),
                    // Then fall back on parameters default values for optional route parameters
                    new DefaultValueResolver(),
                        ]),
                $container
        );
        $controllerInvoker = new ControllerInvoker($invoker);
        $app->getRouteCollector()->setDefaultInvocationStrategy($controllerInvoker);
        return $app;
    },
    ResponseFactoryInterface::class => function (App $app) {
        return $app->getResponseFactory();
    },
    RouteParserInterface::class => function (App $app) {
        return $app->getRouteCollector()->getRouteParser();
    },
    CallableResolverInterface::class => function (App $app) {
        return $app->getCallableResolver();
    },
    //Custom Error Handler
    ErrorHandlerInterface::class => function(ContainerInterface $container, App $app) {
        $handler = $container->get(SlimErrorHandler::class);
        $app->add($handler);
        return $handler;
    },
    TwigMiddleware::class => function(ContainerInterface $container, App $app, Twig $twig) {

        $extensions = $container->get('extensions');

        foreach ($extensions as $extension) {
            $twig->addExtension($extension);
        }

        return new TwigMiddleware(
                $twig,
                $app->getRouteCollector()->getRouteParser(),
                $app->getBasePath()
        );
    },
    Twig::class => function (ContainerInterface $container) {
        $settings = $container->get('settings');
        return Twig::create($settings ['twig.paths'], $settings ['twig.options']);
    },
    "view" => get(Twig::class),
    Guard::class => function (ResponseFactoryInterface $responseFactory) {
        $guard = new Guard($responseFactory);

        $guard->setFailureHandler(function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
                    $request = $request->withAttribute("csrf_status", false);
                    return $handler->handle($request);
                });
        return $guard;
    },
    "csrf" => get(Guard::class),
    BasePathMiddleware::class => function (App $app) {
        return new BasePathMiddleware($app, php_sapi_name());
    },
    LoggerInterface::class => function(ContainerInterface $container) {

        $settings = $container->get('settings');
        $path = $settings['paths.logs'];

        $handlers = [
            new FilterHandler(new StreamHandler($path . "/app.log", Logger::DEBUG), Logger::DEBUG, 399),
            new StreamHandler($path . "/error.log", Logger::ERROR),
        ];
        $processor = new UidProcessor();
        return new Logger($settings['app.title'], $handlers, [$processor]);
    },
    CacheItemPoolInterface::class => function (ContainerInterface $container) {
        $settings = $container->get('settings');
        return new PHPCache($settings['cache.path'], $settings['cache.ttl'], $settings['cache.namespace']);
    },
    Connection::class => function(ContainerInterface $container) {

        $settings = $container->get('settings');
        $name = $settings['db.name'];
        $host = $settings['db.host'];
        $port = $settings['db.port'];
        $dbname = $settings['db.dbname'];
        $user = $settings['db.username'];
        $password = $settings['db.password'];
        $charset = $settings['db.charset'];

        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s;port=%s', $host, $dbname, $charset, $port);

        return new Connection([
            'name' => $name,
            'dsn' => $dsn,
            'username' => $user,
            'password' => $password
        ]);
    },
    CommandMiddleware::class => function(ContainerInterface $container, ResponseFactoryInterface $responseFactory) {
        $middleware = new CommandMiddleware($container, $responseFactory);
        $middleware->displayTraceOnError($container->get('settings')['slim.displayerrordetails']);
        return $middleware;
    }
];
