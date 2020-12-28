<?php

namespace App\Controllers;

use App\Models\{
    Session, User
};
use NGSOFT\Tools\Objects\SessionStorage,
    Psr\Http\Message\ResponseInterface;
use Slim\Http\{
    Response, ServerRequest
};

class Users extends BaseController {

    public function register(ServerRequest $request, Response $response): ResponseInterface {
        return $response;
    }

    public function login(ServerRequest $request): ResponseInterface {
        if ($this->isLoggedIn()) return $this->redirectToRoute("home");
        if (User::countEntries() === 0) return $this->redirectToRoute('user.register');

        $this->title = "User Connection";
        if ($request->getMethod() == "POST") {
            if ($request->getAttribute("csrf_status", true)) $this->setAlertMessage('Invalid Credentials.');
            return $this->redirectToLogin();
        }
        return $this->render('user/login.twig',);
    }

    public function logout(): ResponseInterface {
        $sessionStorage = $this->get(SessionStorage::class);
        if (
                $this->session instanceof Session
        ) {
            $sessionStorage->removeItem('sid');
            $this->session->trash();
            return $this->redirectToRoute('home');
        }
        return $this->createResponse(403);
    }

}
