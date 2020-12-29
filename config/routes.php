<?php

use App\{
    Controllers\BaseController, Controllers\Users, Middlewares\SessionLogin
};
use Slim\{
    App, Http\Response, Routing\RouteCollectorProxy
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
                ->map(['GET', 'POST'], 'register', [Users::class, 'register'])
                ->setName("user.register");

        $group
                ->map(['GET', 'POST'], 'login', [Users::class, 'login'])
                ->setName("user.login")
                ->add(SessionLogin::class);

        $group
                ->map(['GET', 'POST'], 'profile', [Users::class, 'profile'])
                ->setName("user.profile");

        $group
                ->map(['GET', 'POST'], 'admin', [Users::class, 'admin'])
                ->setName("user.admin");

        $group
                ->get("logout", [Users::class, 'logout'])
                ->setName("user.logout");
    });
};
