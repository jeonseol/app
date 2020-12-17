<?php

// Configure defaults for the whole application.
// Error reporting
if (php_sapi_name() !== "cli") session_start();

error_reporting(1);
ini_set('display_errors', '1');

// Timezone
date_default_timezone_set('Europe/Paris');

// Fix MimeType
ini_set('default_mimetype', '');

// Settings
$settings = [];

$settings['paths'] = [
    'root' => dirname(__DIR__),
    'bin' => dirname(__DIR__) . "/bin",
    'config' => dirname(__DIR__) . "/config",
    'docs' => dirname(__DIR__) . "/docs",
    'logs' => dirname(__DIR__) . "/logs",
    'public' => dirname(__DIR__) . "/public",
    'resources' => dirname(__DIR__) . "/resources",
    'src' => dirname(__DIR__) . "/src",
    'templates' => dirname(__DIR__) . "/templates",
    'tests' => dirname(__DIR__) . "/tests",
    'tmp' => dirname(__DIR__) . "/tmp"
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
