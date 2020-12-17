<?php

declare(strict_types=1);

namespace App\Extensions;

use Twig\{
    Extension\AbstractExtension, TwigTest
};

class Tests extends AbstractExtension {

    public function getTests() {
        return [
            new TwigTest("instanceof", [$this, 'instanceOf'])
        ];
    }

    public function getFunctions() {
        return [];
    }

    public function instanceOf($object, string $class) {
        return $object instanceof $class;
    }

}
