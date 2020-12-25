<?php

use App\Models\User,
    Manju\ORM,
    Psr\Log\LoggerInterface;

ORM::setContainer($container);
ORM::addModelPath(...$container->get('settings')->get('db.models'));
ORM::start();

