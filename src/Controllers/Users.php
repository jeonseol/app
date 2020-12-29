<?php

namespace App\Controllers;

use App\Models\{
    Session, User
};
use Manju\Exceptions\ValidationError,
    NGSOFT\Tools\Objects\SessionStorage,
    Psr\Http\Message\ResponseInterface,
    Slim\Http\ServerRequest;

class Users extends BaseController {

    public function register(ServerRequest $request): ResponseInterface {
        if ($this->isLoggedIn()) return $this->redirectToRoute("home");

        $this->title = 'Create a new User';

        /** @var SessionStorage $session */
        $session = $this->get(SessionStorage::class);

        if ($this->canRegister === true) {
            if ($request->getMethod() === "POST") {
                if ($request->getAttribute("csrf_status", true)) {
                    //validation
                    $valid = true;
                    $params = ["username", "email", "password", "confirm"];
                    $values = [];
                    foreach ($params as $param) {
                        $value = $values[$param] = $request->getParam($param);
                        if (empty($value)) {
                            $this->setAlertMessage('Not all fields are filled.');
                            $valid = false;
                        }
                    }
                    if ($valid === true) {
                        if ($values['password'] !== $values ['confirm']) {
                            $valid = false;
                            $this->setAlertMessage("Password not confirmed correctly");
                        } elseif (!User::checkPasswordValid($values['password'])) {
                            $valid = false;
                            $this->setAlertMessage("Invalid Password Supplied");
                        } elseif (!User::checkEmailValid($values['email'])) {
                            $valid = false;
                            $this->setAlertMessage("Invalid Email Supplied");
                        } elseif (User::hasEmail($values['email'])) {
                            $valid = false;
                            $this->setAlertMessage("Email already exists.");
                        } elseif (User::hasName($values['username'])) {
                            $valid = false;
                            $this->setAlertMessage("Username already exists.");
                        }
                    }

                    $session->setItem('validUser', $valid);
                }
                return $this->redirectToRoute('user.register');
            }

            if (
                    isset($this->postdata)
                    and $session->getItem('validUser') === true
            ) {

                $session->removeItem('validUser');

                $params = $this->postdata;
                //User Creation
                $user = User::create();
                $user->name = $params["username"];
                $user->password = $params["password"];
                $user->email = $params["email"];
                try {
                    $user->save(true);
                    $this->success = true;
                } catch (ValidationError $error) {
                    $error->getCode();
                    $this->setAlertMessage('Sorry, an error has occured.');
                    return $this->redirectToRoute('user.register');
                }
            }

            return $this->render('user/register');
        }
        return $this->createResponse(403);
    }

    public function login(ServerRequest $request): ResponseInterface {
        if ($this->isLoggedIn()) return $this->redirectToRoute("home");
        if (User::countEntries() === 0) return $this->redirectToRoute('user.register');

        $this->title = "User Connection";
        if ($request->getMethod() == "POST") {
            if ($request->getAttribute("csrf_status", true)) $this->setAlertMessage('Invalid Credentials.');
            return $this->redirectToLogin();
        }
        return $this->render('user/login');
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
