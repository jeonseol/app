<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\{
    Group, User
};
use JsonException;
use NGSOFT\Tools\Objects\{
    SessionStorage, stdObject
};
use Psr\{
    Container\ContainerInterface, Http\Message\ResponseFactoryInterface, Http\Message\ResponseInterface,
    Http\Message\ServerRequestInterface
};
use Slim\{
    App, Factory\ServerRequestCreatorFactory, Views\Twig
};

class BaseController implements \ArrayAccess, \Countable {

    /**
     * @inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @inject
     * @var App
     */
    protected $app;

    /**
     * @inject
     * @var ResponseFactoryInterface
     */
    protected $responseFactory;

    /**  @var stdObject */
    protected $storage;

    /**
     * @inject ("user")
     * @var User|null
     */
    protected $user;

    ////////////////////////////   DATA   ////////////////////////////

    /** @return array */
    private function getGlobals(): array {
        return $this->get('globals')->toArray();
    }

    /** @return stdObject */
    protected function getStorage(): stdObject {
        if (!($this->storage instanceof stdObject)) {
            $this->storage = stdObject::from($this->getGlobals());
        }
        return $this->storage;
    }

    ////////////////////////////   Renderer   ////////////////////////////

    /**
     * Renders Text Message
     * @param string $message
     * @param ResponseInterface|null $response
     * @return ResponseInterface
     */
    public function renderTextMessage(
            string $message,
            ?ResponseInterface $response = null
    ): ResponseInterface {
        $response = $response ?: $this->createResponse();
        return $this->render('default.twig', ['contents' => $message], $response);
    }

    /**
     * Render the page using the rendering engine
     * @param string $page
     * @param array $data
     * @param ResponseInterface|null $response
     * @return ResponseInterface
     */
    public function render(
            string $page,
            array $data = [],
            ?ResponseInterface $response = null
    ): ResponseInterface {
        $response = $response ?: $this->createResponse();
        if (!preg_match('/\.twig$/', $page)) $page .= '.twig';
        $storage = $this->getStorage()->toArray();
        $data = array_replace([], $storage, $data);
        return $this->get(Twig::class)->render($response, $page, $data);
    }

    /**
     * Write JSON to the response body.
     *
     * This method prepares the response object to return an HTTP JSON
     * response to the client.
     *
     * @param mixed $data The data
     * @param ResponseInterface|null $response
     *
     * @throws JsonException
     *
     * @return ResponseInterface The response
     */
    public function renderJson(
            $data = null,
            ?ResponseInterface $response = null
    ): ResponseInterface {
        $options = 0;
        $response = $response ?: $this->createResponse();
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write((string) json_encode($data, JSON_THROW_ON_ERROR | $options));
        return $response;
    }

    /**
     * Create a new response.
     *
     * @param int $code
     * @return ResponseInterface
     */
    public function createResponse(int $code = 200): ResponseInterface {
        return $this->responseFactory->createResponse($code)->withHeader('Content-Type', 'text/html; charset=utf-8');
    }

    /**
     * Create Request from Globals
     * @return ServerRequestInterface
     */
    public function createRequest(): ServerRequestInterface {
        $serverRequestCreator = ServerRequestCreatorFactory::create();
        return $serverRequestCreator->createServerRequestFromGlobals();
    }

    /**
     * Redirect to specific route
     * @param string $routename
     * @param array $args
     * @return ResponseInterface
     */
    public function redirectToRoute(string $routename, array $args = []): ResponseInterface {
        return $this->responseFactory
                        ->createResponse(302)
                        ->withHeader("Location",
                                $this->app->getRouteCollector()->getRouteParser()
                                ->urlFor($routename, $args)
        );
    }

    /**
     * Redirect to login Route
     * @return ResponseInterface
     */
    protected function redirectToLogin(): ResponseInterface {
        return $this->redirectToRoute("user.login");
    }

    ////////////////////////////   Container   ////////////////////////////

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

    ////////////////////////////   Proxy   ////////////////////////////

    /** {@inheritdoc} */
    public function __get($prop) {
        return $this->getStorage()->__get($prop);
    }

    /** {@inheritdoc} */
    public function __isset($prop) {
        return $this->getStorage()->__isset($prop);
    }

    /** {@inheritdoc} */
    public function __set($prop, $value) {
        $this->getStorage()->__set($prop, $value);
    }

    /** {@inheritdoc} */
    public function __unset($prop) {
        $this->getStorage()->__unset($prop);
    }

    /** {@inheritdoc} */
    public function offsetExists($offset) {
        return $this->getStorage()->offsetExists($offset);
    }

    /** {@inheritdoc} */
    public function offsetGet($offset) {
        if (!is_string($offset)) return null;
        return $this->getStorage()->offsetGet($offset);
    }

    /** {@inheritdoc} */
    public function offsetSet($offset, $value) {
        if (!is_string($offset)) return;
        $this->getStorage()->offsetSet($offset, $value);
    }

    /** {@inheritdoc} */
    public function offsetUnset($offset) {

        $this->getStorage()->offsetUnset($offset);
    }

    /** {@inheritdoc} */
    public function count() {
        return $this->getStorage()->count();
    }

    ////////////////////////////   Utils   ////////////////////////////

    /**
     * Current User Can access Page
     * @param Group $groups
     * @return bool
     */
    protected function userCanAccessContent(Group ... $groups): bool {
        if ($this->user instanceof User) {
            foreach ($groups as $group) {
                if ($this->user->hasGroup($group)) return true;
            }
        }
        return false;
    }

    /**
     * Checks if logged in
     * @return bool
     */
    protected function isLoggedIn(): bool {
        return $this->user instanceof User;
    }

    /**
     * Creates a Flash Message
     * @param string $message
     * @return static
     */
    protected function setFlashMessage(string $message): self {
        $this->get(SessionStorage::class)->setItem('flashMessage', $message);
        return $this;
    }

    /**
     * Creates an Alert Message
     * @param string $message
     * @return static
     */
    protected function setAlertMessage(string $message): self {
        $this->get(SessionStorage::class)->setItem('alertMessage', $message);
        return $this;
    }

    /**
     * Creates an Alert Message
     * @param string $message
     * @return static
     */
    protected function setSuccessMessage(string $message): self {
        $this->get(SessionStorage::class)->setItem('successMessage', $message);
        return $this;
    }

}
