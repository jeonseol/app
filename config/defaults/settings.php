<?php

// Configure defaults for the whole application.
// Error reporting
if (php_sapi_name() !== "cli") session_start();

//error_reporting(0);
ini_set('display_errors', '1');

// Timezone
date_default_timezone_set('Europe/Paris');

// Fix MimeType
ini_set('default_mimetype', '');

// Settings
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

return $settings;
