<?php

use Slim\{
    Http\Response, Http\ServerRequest as Request, Routing\RouteCollectorProxy
};

/*
  $app->any('/', function(Request $request, Response $response) use ($container) {
  print_r($this);
  $controller = new HomeController($container, $request, $response);
  return $controller->home();
  })->setName("home"); */


$app->any('/', function(Request $request, Response $response) use ($container) {
    $controller = new App\Controllers\BaseController($container, $request, $response);
    $controller->title = "Your Slim 4 Project";
    return $controller->renderText('Welcome to your Slim Project');
})->setName("home");


