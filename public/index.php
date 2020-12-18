<?php

declare(strict_types=1);


// Slim App Loader
$root = dirname(__DIR__);

require_once "$root/resources/manju/vendor/autoload.php";

require_once "$root/vendor/autoload.php";

if ($app = require_once "$root/config/config.php") {

    $app->run();
}


