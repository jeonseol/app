<?php

use App\Extensions\{
    BaseUrl, CSRF, Tests, TwigGlobalVars
};
use Psr\Http\{
    Message\ResponseFactoryInterface, Server\RequestHandlerInterface
};
use Selective\BasePath\BasePathMiddleware;
use Slim\{
    Handlers\ErrorHandler, Http\ServerRequest, Interfaces\CallableResolverInterface, Middleware\ErrorMiddleware,
    Middleware\RoutingMiddleware, Views\Twig, Views\TwigMiddleware
};

if (file_exists(dirname(__DIR__) . '/middlewares.php')) require_once dirname(__DIR__) . '/middlewares.php';



$app->add(new RoutingMiddleware($app->getRouteResolver(), $app->getRouteCollector()->getRouteParser()));
//twig Extensions
$app->add(function (ServerRequest $request, RequestHandlerInterface $handler) use ($container, $app) {

    $twig = $container->get(Twig::class);
    if ($twig instanceof Twig) {
        //some functions
        $twig->addExtension(new BaseUrl($request, $app->getBasePath()));


        //global vars
        if (file_exists(__DIR__) . '/twig/globals.php') $data = require __DIR__ . '/twig/globals.php';
        else $data = [];

        $twig->addExtension(new TwigGlobalVars($container, $data));
        $twig->addExtension(new Tests());
        $twig->addExtension($container->get(CSRF::class));

        if (file_exists(__DIR__ . '/twig/extensions.php')) {
            $extensions = require __DIR__ . '/twig/extensions.php';
            if (is_array($extensions)) {
                foreach ($extensions as $ext) {
                    $twig->addExtension($ext);
                }
            }
        }

        $view = new TwigMiddleware(
                $twig,
                $app->getRouteCollector()->getRouteParser(),
                $app->getBasePath()
        );

        return $view->process($request, $handler);
    }

    return $handler->handle($request);
});

$app->add(new BasePathMiddleware($app));

/**
 * Add Error Handling Middleware
 * The constructor of `ErrorMiddleware` takes in 5 parameters
 *
 * @param CallableResolverInterface $callableResolver - CallableResolver implementation of your choice
 * @param ResponseFactoryInterface $responseFactory - ResponseFactory implementation of your choice
 * @param bool $displayErrorDetails - Should be set to false in production
 * @param bool $logErrors - Parameter is passed to the default ErrorHandler
 * @param bool $logErrorDetails - Display error details in error log
 */
$errorMiddleware = new ErrorMiddleware(
        $app->getCallableResolver(),
        $app->getResponseFactory(),
        $container->get("settings")->get('slim.displayerrordetails'),
        $container->get("settings")->get('slim.logerrors'),
        $container->get("settings")->get('slim.logerrordetails')
);
$errorMiddleware->setDefaultErrorHandler($container->get(ErrorHandler::class));
$app->add($errorMiddleware);


