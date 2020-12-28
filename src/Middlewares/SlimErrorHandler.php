<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Controllers\BaseController;
use Psr\{
    Container\ContainerInterface, Http\Message\ResponseInterface, Http\Message\ServerRequestInterface, Http\Server\MiddlewareInterface,
    Http\Server\RequestHandlerInterface, Log\LoggerInterface
};
use Slim\{
    App, Exception\HttpBadRequestException, Exception\HttpForbiddenException, Exception\HttpInternalServerErrorException,
    Exception\HttpMethodNotAllowedException, Exception\HttpNotFoundException, Exception\HttpNotImplementedException,
    Exception\HttpSpecializedException, Exception\HttpUnauthorizedException, Handlers\ErrorHandler
};

class SlimErrorHandler extends ErrorHandler implements MiddlewareInterface {

    /** @var ContainerInterface */
    protected $container;

    /** @var string */
    protected $templates;

    public function __construct(
            ContainerInterface $container,
            App $app
    ) {
        $this->container = $container;
        $this->templates = $container->get('settings')['paths.templates'];

        parent::__construct(
                $app->getCallableResolver(),
                $app->getResponseFactory(),
                $container->get(LoggerInterface::class)
        );
    }

    protected function respond(): ResponseInterface {

        if (preg_match(('/html/'), $this->contentType)) {
            $response = $this->responseFactory
                    ->createResponse($this->statusCode)
                    ->withHeader('Content-type', $this->contentType);

            /** @var BaseController $controller */
            $controller = $this->container->get(BaseController::class);
            $exception = $this->exception;
            $templates = $this->templates;
            $template = "slimerror.twig";

            if ($exception instanceof HttpSpecializedException) {

                //   var_dump($exception->getRequest()->getUri()->getPath());

                $code = $exception->getCode();
                $response = $response->withStatus($code);
                $file = sprintf('codes/%u.twig', $code);
                if (file_exists("$templates/$file")) $template = $file;
            } else $response = $response->withStatus(500);


            $data = [
                'title' => $this->container->get("settings")->get('app.title') . " Error",
                'details' => $this->displayErrorDetails,
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ];

            $response = $controller->render($template, $data, $response);
        } else $response = parent::respond();
        return $response;
    }

    /**
     * Finds empty responses with error status codes
     * and throws the appropriate exception
     *
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        $response = $handler->handle($request);
        $code = $response->getStatusCode();

        $len = $response->getBody()->getSize();
        if ($len === 0) {
            $classname = null;
            switch ($code) {
                case 500:
                    $classname = HttpInternalServerErrorException::class;
                    break;
                case 501:
                    $classname = HttpNotImplementedException::class;
                    break;
                case 400:
                    $classname = HttpBadRequestException::class;
                    break;
                case 401:
                    $classname = HttpUnauthorizedException::class;
                    break;
                case 403:
                    $classname = HttpForbiddenException::class;
                    break;
                case 404:
                    $classname = HttpNotFoundException::class;
                    break;
                case 405:
                    $classname = HttpMethodNotAllowedException::class;
                    break;
            }

            if (is_string($classname)) {
                throw new $classname($request);
            }
        }

        return $response;
    }

}
