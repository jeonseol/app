<?php

namespace App\Middlewares;

use App\{
    Extensions\TwigGlobalVars, Models\Session, Models\User
};
use DI\Container,
    NGSOFT\Tools\Objects\SessionStorage;
use Psr\Http\{
    Message\ResponseInterface, Message\ServerRequestInterface, Server\MiddlewareInterface, Server\RequestHandlerInterface
};
use Slim\{
    App, Http\ServerRequest
};

class UserSession implements MiddlewareInterface {

    /** @var App */
    private $app;

    /** @var Container */
    private $container;

    public function __construct(
            App $app
    ) {
        $this->container = $app->getContainer();
        $this->app = $app;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        $session = $this->container->get(SessionStorage::class);
        Session::CleanUp();

        if ($session->getItem("sid") === null and $request->getMethod() === "POST") {
            if (
                    $request instanceof ServerRequest and count($request->getParams()) > 0
            ) {
                $email = $request->getParam("email");
                $name = $request->getParam("username");
                $pass = $request->getParam("password");
                if ($request->getParam("confirm") === null) {
                    if (
                            isset($email)
                            and isset($pass)
                    ) {
                        //var_dump([$email, $pass]); exit;
                        if ($user = User::getUser($email, $pass)) {
                            $usersession = Session::create();
                            $usersession->user = $user;
                            if ($usersession->save() !== null) $session->setItem("sid", $usersession->sid);
                        }
                    } elseif (
                            isset($name)
                            and isset($pass)
                    ) {
                        if ($user = User::getUserByName($name, $pass)) {
                            $usersession = Session::create();
                            $usersession->user = $user;
                            if ($usersession->save() !== null) $session->setItem("sid", $usersession->sid);
                        }
                    }
                }
            }
        }

        if ($sid = $session->getItem("sid")) {
            if ($usersession = Session::getSession($sid)) {
                TwigGlobalVars::addGlobal("user", $usersession->user);
                $this->container->set("user", $usersession->user);
            } else $session->removeItem("sid");
        }

        return $handler->handle($request);
    }

}
