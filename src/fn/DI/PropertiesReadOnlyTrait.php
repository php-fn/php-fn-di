<?php
/**
 * (c) php-fn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
