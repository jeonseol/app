<?php

namespace App\Utils;

use Psr\Container\ContainerInterface;
use RedBeanPHP\{
    Facade, Logger
};

class SQLLogger implements Logger {

    /** @var string */
    private $file;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(ContainerInterface $container, \Psr\Log\LoggerInterface $logger) {
        $this->logger = $logger;
        $settings = $container->get('settings');
        $this->file = $file = sprintf('%s/migrations/migrations-%s.sql', $settings['paths.resources'], Facade::isoDate());
        if (!is_dir(dirname($file))) @mkdir(dirname($file), 0777, true);
    }

    /** {@inheritdoc} */
    public function log() {
        $query = func_get_arg(0);

        $record = [
            'CREATE', 'ALTER', 'DROP',
            'SELECT', 'INSERT', 'UPDATE', 'DELETE'
        ];

        if (preg_match(sprintf('/^(%s)/i', implode('|', $record)), $query) > 0) {

            $this->logger->debug($query, func_get_args());

            file_put_contents($this->file, "{$query};\n", FILE_APPEND);
        }
    }

}
