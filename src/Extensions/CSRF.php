<?php

declare(strict_types=1);

namespace App\Extensions;

use Slim\Csrf\Guard;
use Twig\{
    Extension\AbstractExtension, Extension\GlobalsInterface, TwigFunction
};

class CSRF extends AbstractExtension implements GlobalsInterface {

    /** @var Guard */
    protected $csrf;

    public function __construct(Guard $csrf) {
        $this->csrf = $csrf;
    }

    public function getHTML(): string {
        return '<input type="hidden" name="'
                . $this->csrf->getTokenNameKey()
                . '" value="'
                . $this->csrf->getTokenName()
                . '">'
                . '<input type="hidden" name="'
                . $this->csrf->getTokenValueKey()
                . '" value="'
                . $this->csrf->getTokenValue()
                . '">';
    }

    public function getGlobals(): array {
        // CSRF token name and value
        $csrfNameKey = $this->csrf->getTokenNameKey();
        $csrfValueKey = $this->csrf->getTokenValueKey();
        $csrfName = $this->csrf->getTokenName();
        $csrfValue = $this->csrf->getTokenValue();

        return [
            'csrf' => [
                'keys' => [
                    'name' => $csrfNameKey,
                    'value' => $csrfValueKey
                ],
                'name' => $csrfName,
                'value' => $csrfValue
            ]
        ];
    }

    public function getFunctions() {
        return [
            new TwigFunction('csrfinput', [$this, 'getHTML'], ['is_safe' => ['html']])
        ];
    }

}
