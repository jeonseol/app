<?php

use App\{
    Controllers\BaseController, Middlewares\SessionLogin
};
use Slim\{
    App, Http\Response, Http\ServerRequest as Request, Routing\RouteCollectorProxy
};

return function(App $app) {


    $app->any('/', function(Response $response) {

        /** @var \DI\Container $this */
        $controller = $this->get(BaseController::class);
        /** @var BaseController $controller */
        $controller->title = "Your Slim 4 Project";
        return $controller->renderTextMessage('Welcome to your Slim Project', $response);
    })->setName("home");

    $app->group('/user/', function (RouteCollectorProxy $group) {

        $group->any("login.html", function (Request $request, Response $response) {
            $controller = new Auth($container, $request, $response);
            return $controller->login();
        })->setName("auth.login")->add(SessionLogin::class);

        $group->any("logout.html", function (Request $request, Response $response) {
            $controller = new Auth($container, $request, $response);
            return $controller->logout();
        })->setName("auth.logout");

        $group->any("register.html", function (Request $request, Response $response) {
            $controller = new Auth($container, $request, $response);
            return $controller->register();
        })->setName("auth.register");
    });
};



