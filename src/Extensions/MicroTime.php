<?php

namespace App\Extensions;

use Twig\{
    Extension\AbstractExtension, TwigFunction
};
use function Micro\exec_time;

class MicroTime extends AbstractExtension {

    public function getFunctions() {
        return [
            new TwigFunction('microtime', [$this, "getMicroTime"]),
            new TwigFunction('querycount', [$this, "getQueryCount"]),
            new TwigFunction('generated', [$this, "pageGenerated"]),
        ];
    }

    public function pageGenerated(): string {
        return sprintf(
                '<small>Page generated in %s seconds using %s queries.</small>',
                $this->getMicroTime(),
                $this->getQueryCount()
        );
    }

    public function getMicroTime(): string {
        return "" . exec_time();
    }

    public function getQueryCount(): string {
        $count = \Manju\ORM::getQueryCount();
        if (is_int($count)) return "$count";
        return "0";
    }

    public function __toString() {
        return $this->pageGenerated();
    }

}
