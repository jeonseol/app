<?php

declare(strict_types=1);

namespace App\Extensions;

use NGSOFT\Tools\Objects\stdObject,
    Psr\Container\ContainerInterface;
use Twig\{
    Extension\AbstractExtension, TwigFunction
};

/**
 * Utilities for Twig
 */
class Utils extends AbstractExtension {

    /** @var stdObject */
    private $globals;

    public function __construct(ContainerInterface $container) {
        $this->globals = $container->get('globals');
    }

    /**
     * Get a global
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getGlobalValue(string $key, $default = null) {
        $value = $this->globals[$key];
        if ($value === null) $value = $default;
        return $value;
    }

    /**
     * Renders an Alert Message
     * @param string|null $message
     * @return string
     */
    public function renderAlertMessage(?string $message = null): string {
        $message = $message ?? $this->globals['alertMessage'];
        if (empty($message)) return '';
        return sprintf('<p class="alert alert-danger">%s</p>', $message);
    }

    /**
     * Renders a Success Message
     * @param string|null $message
     * @return string
     */
    public function renderSuccessMessage(?string $message = null): string {
        $message = $message ?? $this->globals['successMessage'];
        if (empty($message)) return '';
        return sprintf('<p class="alert alert-success">%s</p>', $message);
    }

    /**
     * Renders a Flash Message
     * @param string|null $message
     * @return string
     */
    public function renderFlashMessage(?string $message = null): string {
        $message = $message ?? $this->globals['flashMessage'];
        if (empty($message)) return '';
        return sprintf('<p class="alert alert-info">%s</p>', $message);
    }

    public function getFunctions() {
        $params = ['is_safe' => ['html']];
        return [
            new TwigFunction('flash', [$this, 'renderFlashMessage'], $params),
            new TwigFunction('alert', [$this, 'renderAlertMessage'], $params),
            new TwigFunction('success', [$this, 'renderSuccessMessage'], $params),
        ];
    }

}
