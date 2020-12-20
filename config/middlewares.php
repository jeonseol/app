<?php

declare(strict_types=1);

use App\{
    Middlewares\PostData, Middlewares\SessionLoader, Models\User
};
use Manju\ORM;
use Psr\{
    Container\ContainerInterface, Http\Message\ResponseFactoryInterface, Http\Server\RequestHandlerInterface, Log\LoggerInterface
};
use Selective\BasePath\BasePathMiddleware;
use Slim\{
    App, Csrf\Guard, Http\ServerRequest, Interfaces\CallableResolverInterface, Interfaces\ErrorHandlerInterface,
    Middleware\ErrorMiddleware, Views\Twig, Views\TwigMiddleware
};

$app->addBodyParsingMiddleware();




//$app->add(new RoutingMiddleware($app->getRouteResolver(), $app->getRouteCollector()->getRouteParser()));
$app->addRoutingMiddleware();

$app->add(SessionLoader::class);

/* to add into auth route
  $app->add(SessionLogin::class); */

$app->add(PostData::class);
$app->add(Guard::class);

$app->add(BasePathMiddleware::class);

$app->add(function (ServerRequest $request, RequestHandlerInterface $handler) {

    $settings = $this->get('settings');
    $model_paths = $settings->get('db.models');
    $strong = $settings->get('db.strongpasswords');


    try {
        //db Connection
        ORM::setContainer($this->get(ContainerInterface::class));
        ORM::addModelPath(...$model_paths);
        ORM::start();
        //create admin
        if (User::countEntries() == 0) {
            $user = User::create();
            $user->name = 'admin';
            $user->password = $strong ? 'Passw0rd' : 'admin';
            $user->save();
        }
    } catch (Exception $err) {
        $this->get(LoggerInterface::class)->error($err->getMessage());
        throw $err;
    }


    return $handler->handle($request);
});


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
$errorMiddleware->setDefaultErrorHandler($container->get(ErrorHandlerInterface::class));
$app->add($errorMiddleware);

//twig Extensions
$app->add(function (ServerRequest $request, RequestHandlerInterface $handler) {

    $container = $this->get(ContainerInterface::class);
    $app = $container->get(App::class);
    $twig = $container->get(Twig::class);

    if ($twig instanceof Twig) {

        if (file_exists(__DIR__ . '/twig/globals.php')) $data = require __DIR__ . '/twig/globals.php';
        else $data = [];
        foreach ($data as $key => $value) {
            $twig->getEnvironment()->addGlobal($key, $value);
        }

        if (file_exists(__DIR__ . '/twig/extensions.php')) {
            $extensions = require __DIR__ . '/twig/extensions.php';
            if (is_array($extensions)) {
                foreach ($extensions as $classname) {
                    $twig->addExtension($this->get($classname));
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


