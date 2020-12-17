<?php

use Psr\Log\LoggerInterface;

// Slim App Loader
$root = dirname(__DIR__);

require_once "$root/vendor/autoload.php";

if ($app = require_once "$root/config/config.php") {

    $app->run();
}


