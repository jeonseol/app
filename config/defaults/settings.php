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

return $settings;
