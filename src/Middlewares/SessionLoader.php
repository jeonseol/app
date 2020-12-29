<?php

namespace App\Middlewares;

use App\Models\{
    Session, User
};
use DI\Container;
use NGSOFT\Tools\Objects\{
    SessionStorage, stdObject
};
use Psr\{
    Container\ContainerInterface, Http\Message\ResponseInterface, Http\Message\ServerRequestInterface, Http\Server\MiddlewareInterface,
    Http\Server\RequestHandlerInterface
};

class SessionLoader implements MiddlewareInterface {

    /** @var ContainerInterface */
    protected $container;

    /** @var SessionStorage */
    protected $sessionStorage;

    /** @var stdObject */
    protected $globals;

    public function __construct(ContainerInterface $container, SessionStorage $sessionStorage) {
        $this->container = $container;
        $this->sessionStorage = $sessionStorage;
        $this->globals = $container->get('globals');
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        $session = $this->sessionStorage;
        Session::CleanUp();
        if ($sid = $session->getItem("sid")) {
            if ($usersession = Session::getSession($sid)) {
                $globals = $this->container->get('globals');
                $globals['session'] = $usersession;
                $globals['user'] = $usersession->user;
            } else $session->removeItem("sid");
        }

        if ($this->globals['canRegister'] === null) {


            $settings = $this->container->get('settings');
            $canRegister = $settings['users.can_register'] ?? true;
            $maxCount = $settings['users.max_user_count'] ?? 0;

            if (
                    ($maxCount > 0)
                    and (User::countEntries() >= $maxCount)
            ) {
                $canRegister = false;
            }
            $this->globals['canRegister'] = $canRegister;
        }

        return $handler->handle($request);
    }

}
