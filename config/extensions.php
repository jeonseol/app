<?php

use App\Extensions\{
    CSRF, Tests, Utils
};
use DI\Container;

/**
 * @suppress PhanUnusedClosureParameter
 */
return function (Container $container) {

    return [
        Tests::class,
        CSRF::class,
        Utils::class,
    ];
};

