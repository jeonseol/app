<?php

$settings = require __DIR__ . '/defaults/settings.php';

//Custom Settings here

$settings['app'] = [
    'title' => "My Slim App",
    'tz' => 'Europe/Paris',
];




//error_reporting(0);
//ini_set('display_errors', '0');
// -------------------
return $settings;
