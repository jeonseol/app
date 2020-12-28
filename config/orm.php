<?php

use Manju\ORM,
    Psr\Container\ContainerInterface;

return function(ContainerInterface $container) {
    ORM::setContainer($container);
    ORM::addModelPath(...$container->get('settings')['db.models']);
    ORM::start();
};
