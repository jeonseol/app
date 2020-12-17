<?php

namespace App\Extensions;

use Psr\Container\ContainerInterface,
    Slim\Views\Twig;
use Twig\Extension\{
    AbstractExtension, GlobalsInterface
};

class TwigGlobalVars extends AbstractExtension implements GlobalsInterface {

    /** @var array<string,mixed> */
    private static $data = [];

    /** @var ContainerInterface */
    private $container;

    public function __construct(
            ContainerInterface $container,
            array $data = []
    ) {
        $this->container = $container;

        foreach ($data as $k => $v) {
            static::$data[$k] = $v;
        }
    }

    /**
     * Add a global var to twig environement
     * @param string $name
     * @param mixed $value
     */
    public static function addGlobal(string $name, $value) {
        $this->container->get(Twig::class)
                ->getEnvironment()
                ->addGlobal($name, $value);
    }

    public function getGlobals(): array {
        return static::$data;
    }

}
