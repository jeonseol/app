<?php

// Configure defaults for the whole application.


$settings = [];

$root = dirname(dirname(__DIR__));

$settings['paths'] = [
    'root' => $root,
    'bin' => $root . "/bin",
    'config' => $root . "/config",
    'docs' => $root . "/docs",
    'logs' => $root . "/logs",
    'public' => $root . "/public",
    'resources' => $root . "/resources",
    'src' => $root . "/src",
    'templates' => $root . "/templates",
    'tests' => $root . "/tests",
    'tmp' => $root . "/tmp"
];


$settings['slim'] = [
    'displayerrordetails' => true, //For debugging
    'logerrordetails' => false,
    'logerrors' => true
];

$settings['twig'] = [
    'paths' => [
        $settings['paths']['templates'],
        $settings['paths']['src'] . '/Views'
    ],
    'options' => [
        'cache' => $settings['paths']['tmp'] . '/twig',
        'auto_reload' => true,
        'debug' => false
    ]
];

$settings['cache'] = [
    'path' => $settings['paths']['tmp'] . '/phpcache',
    'ttl' => 5 * minute,
    'namespace' => 'slimapp'
];


$settings['db'] = [
    'models' => [
        $settings['paths']['src'] . '/Models'
    ],
    'host' => getenv('dbhost') ?? 'localhost',
    'port' => intval(getenv('dbport') ?? 3306),
    'dbname' => getenv('dbname') ?? 'my_slim_app',
    'user' => getenv('dbuser') ?? 'root',
    'password' => getenv('dbpassword') ?? 'root',
    'charset' => getenv('dbcharset') ?? 'utf8mb4',
    'strongpasswords' => getenv('dbstrongpasswords') == 'true'
];


return $settings;
