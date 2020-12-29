<?php

use App\Utils\SQLLogger,
    Manju\ORM,
    Psr\Container\ContainerInterface,
    RedBeanPHP\Facade;

return function(ContainerInterface $container) {
    ORM::setContainer($container);
    ORM::addModelPath(...$container->get('settings')['db.models']);
    ORM::start();

    $ml = $container->get(SQLLogger::class);
    Facade::getDatabaseAdapter()
            ->getDatabase()
            ->setLogger($ml)
            ->setEnableLogging(true);
};
