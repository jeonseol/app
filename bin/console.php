#!/usr/bin/env php
<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

if ($app = require_once dirname(__DIR__) . "/config/config.php") {
    $app->run();
}
