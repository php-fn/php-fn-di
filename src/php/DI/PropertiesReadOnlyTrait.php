<?php
/**
 * Copyright (C) php-fn. See LICENSE file for license details.
 */

namespace php\DI;

use php;

trait PropertiesReadOnlyTrait
{
    use PropertiesReadWriteTrait;

    /**
     * @inheritdoc
     */
    public function __set($name, $value): void
    {
        php\fail('class %s has read-only access for magic-properties: %s', static::class, $name);
    }

    /**
     * @inheritdoc
     */
    public function __unset($name): void
    {
        php\fail('class %s has read-only access for magic-properties: %s', static::class, $name);
    }
}