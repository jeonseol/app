<?php

declare(strict_types=1);

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
    private $twig;

    public function __construct(
            Twig $twig
    ) {
        $this->twig = $twig;
    }

    /**
     * Add a global var to twig environement
     * @param string $name
     * @param mixed $value
     */
    public static function addGlobal(string $name, $value) {
        $this->twig
                ->getEnvironment()
                ->addGlobal($name, $value);
    }

    public function getGlobals(): array {
        return static::$data;
    }

}
