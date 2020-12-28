<?php

use App\{
    Controllers\BaseController, Middlewares\SessionLogin
};
use DI\Container;
use Slim\{
    App, Http\Response, Http\ServerRequest as Request, Routing\RouteCollectorProxy
};

/** @var Container $container */
/** @var App $app */
$app->any('/', function(Request $request, Response $response) {

    /** @var Container $this */
    $controller = $this->get(BaseController::class);
    /** @var BaseController $controller */
    $controller->title = "Your Slim 4 Project";
    return $controller->renderTextMessage('Welcome to your Slim Project');
})->setName("home");

$app->group('/user/', function (RouteCollectorProxy $group) use ($container) {

    $group->any("login.html", function (Request $request, Response $response) use($container) {
        $controller = new Auth($container, $request, $response);
        return $controller->login();
    })->setName("auth.login")->add(SessionLogin::class);

    $group->any("logout.html", function (Request $request, Response $response) use($container) {
        $controller = new Auth($container, $request, $response);
        return $controller->logout();
    })->setName("auth.logout");

    $group->any("register.html", function (Request $request, Response $response) use($container) {
        $controller = new Auth($container, $request, $response);
        return $controller->register();
    })->setName("auth.register");
});


