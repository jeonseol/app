<?php

declare(strict_types=1);

namespace App\Controllers;

use JsonException,
    NGSOFT\Tools\Objects\stdObject;
use Psr\{
    Container\ContainerInterface, Http\Message\ResponseInterface, Http\Message\ServerRequestInterface
};
use Slim\{
    App, Factory\ServerRequestCreatorFactory, Http\Response, Http\ServerRequest
};

class BaseController {

    /** @var array<string,mixed> */
    protected static $globals = [];

    /** @var ContainerInterface */
    protected $container;

    /** @var App */
    protected $app;

    /** @var RespinseFactoryInterface */
    protected $responseFactory;

    /** @var ServerRequestInterface */
    protected $request;

    /** @var ResponseInterface */
    protected $response;

    /** @var stdObject */
    protected $data;

    /** @var User|null */
    protected $user;

    /**
     * Add Global available to all controllers
     * @param string $name
     * @param mixed $value
     */
    public static function addGlobal(string $name, $value) {
        self::$globals[$name] = $value;
    }

    /**
     * Adds multiple globals
     * @param iterable $data
     */
    public static function addGlobals(iterable $data) {
        foreach ($data as $k => $v) {
            if (is_string($k)) self::addGlobal($k, $v);
        }
    }

    public function __construct(
            ContainerInterface $container,
            ServerRequest $request = null,
            Response $response = null
    ) {

        $this->container = $container;
        $this->app = $container->get(App::class);
        $this->responseFactory = $this->app->getResponseFactory();

        if (!$request) {
            $serverRequestCreator = ServerRequestCreatorFactory::create();
            $request = $serverRequestCreator->createServerRequestFromGlobals();
        }

        if (!$response) $response = $this->createResponse();
        $this->request = $request;
        $this->response = $response;
        $this->data = stdObject::create();
        if ($this->container->has("user")) $this->user = $this->container->get("user");
    }

    /**
     * Get given entry from container
     * @param string $key
     * @return mixed
     */
    protected function get(string $key) {
        return $this->container->get($key);
    }

    /**
     * Check if container has given entry
     * @param string $key
     * @return bool
     */
    protected function has(string $key): bool {
        return $this->container->has($key);
    }

    /**
     * Renders Text
     * @param string $contents
     * @return ResponseInterface
     */
    public function renderText(string $contents): ResponseInterface {
        return $this->render('default.twig', ['contents' => $contents]);
    }

    /**
     * Render the page using the rendering engine
     * @param ResponseInterface $response
     * @param string $page
     * @param array $data
     * @return ResponseInterface
     */
    public function render(string $page, array $data = []): ResponseInterface {
        $data = array_replace(self::$globals, $this->data->toArray(), $data);
        return $this->get("view")->render($this->response, $page, $data);
    }

    /**
     * Write JSON to the response body.
     *
     * This method prepares the response object to return an HTTP JSON
     * response to the client.
     *
     * @param ResponseInterface $response The response
     * @param mixed $data The data
     * @param int $options Json encoding options
     *
     * @throws JsonException
     *
     * @return ResponseInterface The response
     */
    public function renderJson(
            ResponseInterface $response,
            $data = null,
            int $options = 0
    ): ResponseInterface {
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write((string) json_encode($data, JSON_THROW_ON_ERROR | $options));
        return $response;
    }

    /**
     * Create a new response.
     *
     * @return ResponseInterface The response
     */
    public function createResponse(): ResponseInterface {
        return $this->responseFactory->createResponse()->withHeader('Content-Type', 'text/html; charset=utf-8');
    }

    /**
     * Redirect to specific route
     * @param string $routename
     * @param array $args
     * @return ResponseInterface
     */
    public function redirectToRoute(string $routename, array $args = []): ResponseInterface {
        return $this->app->getResponseFactory()
                        ->createResponse(302)
                        ->withHeader("Location",
                                $this->app->getRouteCollector()->getRouteParser()
                                ->urlFor($routename, $args)
        );
    }

    ////////////////////////////   Proxy   ////////////////////////////

    /** {@inheritdoc} */
    public function __get($prop) {
        return $this->data->__get($prop);
    }

    /** {@inheritdoc} */
    public function __isset($prop) {
        return $this->data->__isset($prop);
    }

    /** {@inheritdoc} */
    public function __set($prop, $value) {
        $this->data->__set($prop, $value);
    }

    /** {@inheritdoc} */
    public function __unset($prop) {
        $this->data->__unset($prop);
    }

}
