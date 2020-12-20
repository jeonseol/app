<?php

namespace App\Middlewares;

use App\Models\{
    Session, User
};
use NGSOFT\Tools\Objects\SessionStorage;
use Psr\Http\{
    Message\ResponseInterface, Message\ServerRequestInterface, Server\RequestHandlerInterface
};
use Slim\Http\ServerRequest;

class SessionLogin extends SessionLoader {

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        if ($request->getAttribute('csrf_status') !== false) {

            $session = $this->container->get(SessionStorage::class);
            if ($session->getItem("sid") === null and $request->getMethod() === "POST") {
                if (
                        $request instanceof ServerRequest
                        and count($request->getParams()) > 0
                ) {
                    $email = $request->getParam("email");
                    $name = $request->getParam("username");
                    $pass = $request->getParam("password");
                    if ($request->getParam("confirm") === null) {
                        if (
                                isset($email)
                                and isset($pass)
                        ) {

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
                                if ($usersession->save()) {
                                    $session->setItem("sid", $usersession->sid);
                                }
                            }
                        }
                        $session->removeItem('postdata');
                        return parent::process($request, $handler);
                    }
                }
            }
        }
        return $handler->handle($request);
    }

}
