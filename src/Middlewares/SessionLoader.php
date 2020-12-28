<?php

namespace App\Middlewares;

use App\Models\Session,
    NGSOFT\Tools\Objects\SessionStorage;
use Psr\{
    Container\ContainerInterface, Http\Message\ResponseInterface, Http\Message\ServerRequestInterface, Http\Server\MiddlewareInterface,
    Http\Server\RequestHandlerInterface
};

class SessionLoader implements MiddlewareInterface {

    /** @var ContainerInterface */
    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $session = $this->container->get(SessionStorage::class);

        Session::CleanUp();
        if ($sid = $session->getItem("sid")) {
            if ($usersession = Session::getSession($sid)) {
                $globals = $this->container->get('globals');
                $globals['user'] = $usersession->user;
            } else $session->removeItem("sid");
        }
        return $handler->handle($request);
    }

}
