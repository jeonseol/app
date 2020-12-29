<?php

declare(strict_types=1);

namespace App\Utils;

use Psr\Container\ContainerInterface;
use RedBeanPHP\{
    Facade, Logger
};

class SQLLogger implements Logger {

    /** @var string */
    private $file;

    public function __construct(ContainerInterface $container) {

        $dest = sprintf('%s/migrations/migrations-%s.sql', $container->get('settings')['paths.resources'], Facade::isoDate());
        if (!is_dir(dirname($dest))) @mkdir(dirname($dest), 0777, true);
        $this->file = $dest;
    }

    public function log() {

        if (empty($this->file)) return;

        $query = func_get_arg(0);
        $statements = [
            'CREATE', 'ALTER', 'DROP',
            'SELECT', 'INSERT', 'UPDATE', 'DELETE',
        ];
        if (preg_match(sprintf('/^(%s)/', implode('|', $statements)), $query) > 0) {
            file_put_contents($this->file, sprintf("%s;\n", $query), FILE_APPEND);
        }
    }

}
