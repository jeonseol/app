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

        if ($this->isLoggedIn()) {

        }

        $response = $response->withHeader('Content-Type', 'application/json');
        // $request = $request->withHeader('Accept', 'application/json');

        if (
                ($request->getMethod() == "POST")
                and!$request->getAttribute("csrf_status", true)
        ) {
            $this->flash = "CSRF Verification Failed";

            return $response->withStatus(400);
        }
        return $this->renderJson([
                    "method" => $request->getMethod(),
                    "params" => $request->getParams(),
                    "attributes" => $request->getAttributes()
                        ], $response);
        return $response;
    }

    public function logout(ServerRequest $request, Response $response): ResponseInterface {
        if ($this->isLoggedIn()) {
            $session = $this->get(SessionStorage::class);
            $session->removeItem("sid");
        }
        return $this->redirectToRoute('home');
    }

}
