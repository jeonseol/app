<?php

declare(strict_types=1);

use Dotenv\Dotenv;

@define('APP_ROOT', dirname(__DIR__));
@define('APP_BIN', dirname(__DIR__) . "/bin");
@define('APP_CONFIG', dirname(__DIR__) . "/config");
@define('APP_DOCS', dirname(__DIR__) . "/docs");
@define('APP_LOGS', dirname(__DIR__) . "/logs");
@define('APP_PUBLIC', dirname(__DIR__) . "/public");
@define('APP_RESOURCES', dirname(__DIR__) . "/resources");
@define('APP_SRC', dirname(__DIR__) . "/src");
@define('APP_TEMPLATES', dirname(__DIR__) . "/templates");
@define('APP_TESTS', dirname(__DIR__) . "/tests");
@define('APP_TMP', dirname(__DIR__) . "/tmp");


(Dotenv::createUnsafeImmutable(APP_ROOT))->load();


