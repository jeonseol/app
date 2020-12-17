<?php

use Slim\App;

// Slim App Loader
$root = dirname(__DIR__);

require_once "$root/vendor/autoload.php";



if (isset($app)) $app->run();