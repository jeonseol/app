<?php

declare(strict_types=1);

use App\Middlewares\{
    PostData, SessionLoader
};
use DI\Container,
    NGSOFT\Commands\CommandMiddleware,
    Psr\Http\Server\RequestHandlerInterface,
    Selective\BasePath\BasePathMiddleware;
use Slim\{
    App, Csrf\Guard, Exception\HttpBadRequestException, Exception\HttpForbiddenException, Exception\HttpInternalServerErrorException,
    Exception\HttpMethodNotAllowedException, Exception\HttpNotFoundException, Exception\HttpNotImplementedException,
    Exception\HttpUnauthorizedException, Http\ServerRequest, Interfaces\ErrorHandlerInterface, Middleware\ContentLengthMiddleware,
    Middleware\ErrorMiddleware, Views\TwigMiddleware
};

return function(App $app) {
    /** @var Container $container */
    $container = $app->getContainer();


    $app->add(ContentLengthMiddleware::class);
    $app->addBodyParsingMiddleware();

    $app->addRoutingMiddleware();


    $app->add(SessionLoader::class);

    /* to add into auth route
      $app->add(SessionLogin::class); */

    $app->add(PostData::class);
    $app->add(Guard::class);

    $app->add(BasePathMiddleware::class);




    /**
     * Add Error Handling Middleware
     * The constructor of `ErrorMiddleware` takes in 5 parameters
     *
     * CallableResolverInterface $callableResolver - CallableResolver implementation of your choice
     * ResponseFactoryInterface $responseFactory - ResponseFactory implementation of your choice
     * bool $displayErrorDetails - Should be set to false in production
     * bool $logErrors - Parameter is passed to the default ErrorHandler
     * bool $logErrorDetails - Display error details in error log
     */
    $errorMiddleware = new ErrorMiddleware(
            $app->getCallableResolver(),
            $app->getResponseFactory(),
            $container->get("settings")['slim.displayerrordetails'],
            $container->get("settings")['slim.logerrors'],
            $container->get("settings")['slim.logerrordetails']
    );
    $errorMiddleware->setDefaultErrorHandler($container->get(ErrorHandlerInterface::class));
    $app->add($errorMiddleware);


//twig Extensions
    $app->add(TwigMiddleware::class);

    $app->add(CommandMiddleware::class);
};



