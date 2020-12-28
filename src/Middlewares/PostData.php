<?php

namespace App\Middlewares;

use NGSOFT\Tools\Objects\{
    SessionStorage, stdObject
};
use Psr\{
    Container\ContainerInterface, Http\Message\ResponseInterface, Http\Message\ServerRequestInterface, Http\Server\MiddlewareInterface,
    Http\Server\RequestHandlerInterface
};
use Slim\Http\ServerRequest;

class PostData implements MiddlewareInterface {

    /** @var stdObject */
    private $globals;

    /** @var SessionStorage */
    private $session;

    public function __construct(ContainerInterface $container) {
        $this->globals = $container->get('globals');
        $this->session = $container->get(SessionStorage::class);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {


        $session = $this->session;
        foreach (['postdata', 'flashMessage', 'alertMessage', 'successMessage'] as $prop) {

            if ($data = $session->getItem($prop)) {
                $this->globals[$prop] = $data;
                $session->removeItem($prop);
            }
        }



        if (
                in_array($request->getMethod(), ['POST'])
                and ($request->getAttribute('csrf_status', true) === false)
        ) {
            $this->session->setItem('alert', 'CSRF Verification Failed.');
        }

        if (
                in_array($request->getMethod(), ['POST'])
                and ($request->getAttribute('csrf_status', true) !== false)
                and ($request instanceof ServerRequest)
        ) {
            $this->session->setItem('postdata', $request->getParams());
        }

        return $handler->handle($request);
    }

}
