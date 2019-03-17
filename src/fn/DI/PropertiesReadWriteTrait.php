<?php
/**
 * Copyright (C) php-fn. See LICENSE file for license details.
 */

namespace fn\DI;

use fn;
use Psr\Container\ContainerInterface;

/**
 */
trait PropertiesReadWriteTrait
{
    /**
     * @param string $type
     * @return ContainerInterface|\DI\Container
     */
    private function _getContainer(string $type = ContainerInterface::class): ContainerInterface
    {
        if (method_exists($this, 'getContainer')) {
            $container = $this->getContainer();
        } else if (property_exists($this, 'container')) {
            $container = $this->container;
        } else {
            fn\fail("property %s or method % doesn't exist", 'container', 'getContainer');
        }
        /** @noinspection PhpUndefinedVariableInspection */
        if (!$container instanceof $type) {
            fn\fail('container is not of type %s', $type);
        }
        return $container;
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        $this->_getContainer(\DI\Container::class)->set($name, $value);
    }

    /**
     * @param string $name
     */
    public function __unset($name)
    {
        $this->__set($name, null);
    }

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->_getContainer()->get($name);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return $this->_getContainer()->has($name);
    }
}
