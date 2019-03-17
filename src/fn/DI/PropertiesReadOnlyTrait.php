<?php
/**
 * Copyright (C) php-fn. See LICENSE file for license details.
 */

namespace fn\DI;

use fn;

trait PropertiesReadOnlyTrait
{
    use PropertiesReadWriteTrait;

    /**
     * @inheritdoc
     */
    public function __set($name, $value)
    {
        fn\fail('class %s has read-only access for magic-properties: %s', static::class, $name);
    }

    /**
     * @inheritdoc
     */
    public function __unset($name)
    {
        fn\fail('class %s has read-only access for magic-properties: %s', static::class, $name);
    }
}
