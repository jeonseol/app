<?php

use Manju\ORM,
    Psr\Container\ContainerInterface;

return function(ContainerInterface $container) {
    ORM::setContainer($container);
    ORM::addModelPath(...$container->get('settings')->get('db.models'));
    ORM::start();
};
