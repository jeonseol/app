<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\{
    Response, ServerRequest
};

class Users extends BaseController {

    public function register(ServerRequest $request, Response $response): ResponseInterface {
        return $response;
    }

    public function login(ServerRequest $request, Response $response): ResponseInterface {

        if ($this->isLoggedIn()) return $this->redirectToRoute("home");
        $this->title = "User Connection";

        if ($request->getMethod() == "POST") {
            if ($request->getAttribute("csrf_status", true)) {
                $flash = "CSRF Verification Failed";
            }

            $params = $request->getParams();
        }




        // $request = $request->withHeader('Accept', 'application/json');






        return $this->render('user/login.twig');
    }

    public function logout(ServerRequest $request, Response $response): ResponseInterface {
        if ($this->isLoggedIn()) {
            $session = $this->get(SessionStorage::class);
            $session->removeItem("sid");
        }
        return $this->redirectToRoute('home');
    }

}
