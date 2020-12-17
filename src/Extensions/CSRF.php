<?php

namespace App\Extensions;

use Slim\Csrf\Guard;
use Twig\Extension\{
    AbstractExtension, GlobalsInterface
};

class CSRF extends AbstractExtension implements GlobalsInterface {

    /** @var Guard */
    protected $csrf;

    public function __construct(Guard $csrf) {
        $this->csrf = $csrf;
    }

    public function getHTML() {
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

    public function getGlobals() {
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
