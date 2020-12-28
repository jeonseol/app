<?php

declare(strict_types=1);

namespace App\Models;

use ArrayAccess,
    ArrayObject;
use Manju\{
    ORM, ORM\Model
};

abstract class BaseModel extends Model {

    /**
     * Get Slim Settings
     * @return ArrayAccess
     */
    protected static function getSettings(): ArrayAccess {

        if (
                ($container = ORM::getContainer())
                and $container->has('settings')
        ) {
            $settings = $container->get('settings');
            if ($settings instanceof ArrayAccess) return $settings;
        }
        return new ArrayObject();
    }

}
