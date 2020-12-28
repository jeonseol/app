<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Controllers\BaseController;
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
            ContainerInterface $container
    ) {
        $this->container = $container;
        parent::__construct(
                $container->get(CallableResolverInterface::class),
                $container->get(ResponseFactoryInterface::class),
                $container->get(LoggerInterface::class)
        );
    }

    protected function respond(): ResponseInterface {



        if (preg_match(('/html/'), $this->contentType)) {
            $response = $this->responseFactory
                    ->createResponse($this->statusCode)
                    ->withHeader('Content-type', $this->contentType);



            $controller = $this->container->get(BaseController::class);

            $data = [
                'title' => $this->container->get("settings")->get('app.title') . " Error",
                'details' => $this->displayErrorDetails,
                'message' => $this->exception->getMessage(),
                'code' => $this->exception->getCode(),
                'file' => $this->exception->getFile(),
                'line' => $this->exception->getLine(),
                'trace' => $this->exception->getTraceAsString(),
            ];
            if ($this->exception instanceof HttpNotFoundException) {
                $response = $controller->render("404.twig", $data, $response->withStatus(404));
            } else $response = $controller->render("slimerror.twig", $data, $response->withStatus(500));
        } else $response = parent::respond();


        return $response;
    }

}
