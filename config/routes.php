<?php

use App\{
    Controllers\BaseController, Controllers\Users, Middlewares\SessionLogin
};
use Slim\{
    App, Http\Response, Http\ServerRequest as Request, Routing\RouteCollectorProxy
};

return function(App $app) {

    // 403 access
    $app->any('/assets[/{opt:.*}]', function(Response $response) {
        return $response->withStatus(403);
    });

    $app->any('/', function(Response $response) {

        /** @var \DI\Container $this */
        $controller = $this->get(BaseController::class);
        /** @var BaseController $controller */
        $controller->title = "Your Slim 4 Project";
        return $controller->renderTextMessage('Welcome to your Slim Project', $response);
    })->setName("home");

    // php-di magics
    $app->group('/user/', function (RouteCollectorProxy $group) {

        $group
                ->map(['GET', 'POST'], 'login', [Users::class, 'login'])
                ->setName("user.login")
                ->add(SessionLogin::class);

        $group
                ->get("logout", [Users::class, 'logout'])
                ->setName("user.logout");

        $group
                ->map(['GET', 'POST'], 'register', [Users::class, 'register'])
                ->setName("user.register");
    });
};



