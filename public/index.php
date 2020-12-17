<?php

// Slim App Loader
$root = dirname(__DIR__);

require_once "$root/vendor/autoload.php";

if (isset($app) and $app instanceof \Slim\App) $app->run();