<?php

declare(strict_types=1);

namespace App\Middlewares;

use Psr\{
    Container\ContainerInterface, Http\Message\ResponseFactoryInterface, Http\Message\ResponseInterface, Log\LoggerInterface
};
use Slim\{
    Exception\HttpNotFoundException, Handlers\ErrorHandler, Interfaces\CallableResolverInterface
};

class SlimErrorHandler extends ErrorHandler {

    /** @var ContainerInterface */
    protected $container;

    public function __construct(
            CallableResolverInterface $callableResolver,
            ResponseFactoryInterface $responseFactory,
            LoggerInterface $logger,
            ContainerInterface $container
    ) {
        $this->container = $container;
        parent::__construct($callableResolver, $responseFactory, $logger);
    }

    protected function respond(): ResponseInterface {

        if (preg_match(('/html/'), $this->contentType)) {
            $response = $this->responseFactory->createResponse($this->statusCode);
            $response = $response->withHeader('Content-type', $this->contentType);

            $controller = $this->container->get(\App\Controllers\BaseController::class);

            $data = [
                'title' => $this->container->get("settings")->get('app.title') . " Error",
                'details' => $this->displayErrorDetails,
                'message' => $this->exception->getMessage(),
                'code' => $this->exception->getCode(),
                'file' => $this->exception->getFile(),
                'line' => $this->exception->getLine(),
                'trace' => $this->exception->getTraceAsString(),
            ];
            if ($this->exception instanceof HttpNotFoundException) $response = $controller->render("404.twig", $data);
            else $response = $controller->render("slimerror.twig", $data);
        } else $response = parent::respond();


        return $response;
    }

}
