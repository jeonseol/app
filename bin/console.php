#!/usr/bin/env php
<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

if ($app = require_once dirname(__DIR__) . "/config/config.php") {
    $app->run();
}
