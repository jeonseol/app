<?php

use App\Extensions\{
    CSRF, Tests
};
use DI\Container;

/**
 * @suppress PhanUnusedClosureParameter
 */
return function (Container $container) {

    return [
        Tests::class,
        CSRF::class,
    ];
};

