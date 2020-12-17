<?php

namespace App\Controllers;

use Psr\{
    Container\ContainerInterface, Http\Message\ResponseFactoryInterface, Http\Message\ServerRequestInterface
};
use Slim\{
    App, Factory\ServerRequestCreatorFactory
};

class BaseController {

    /** @var ContainerInterface */
    protected $container;

    /** @var App */
    protected $app;

    /** @var ServerRequestInterface */
    protected $request;

    /** @var ResponseFactoryInterface */
    protected $response;

    public function __construct(
            ContainerInterface $container,
            ?ServerRequest $request = null,
            ?Response $response = null
    ) {

        $this->container = $container;
        $this->app = $container->get(App::class);

        if (!$request) {
            $serverRequestCreator = ServerRequestCreatorFactory::create();
            $request = $serverRequestCreator->createServerRequestFromGlobals();
        }

        if (!$response) $response = $this->app->getResponseFactory()->createResponse();
        $this->request = $request;
        $this->response = $response;
    }

}
