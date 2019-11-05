<?php
/**
 * Copyright (C) php-fn. See LICENSE file for license details.
 */

namespace php;

use DI\CompiledContainer;
use DI\Definition\Source\DefinitionSource;
use Psr\Container\ContainerInterface;

abstract class DI
{
    public const WIRING = 'wiring';
    public const CACHE = 'cache';
    public const PROXY = 'proxy';
    public const COMPILE = 'compile';

    /**
     * Create a container from the given definitions.
     * If the last parameter is a callable it will be invoked to get the container configuration.
     * If the last parameter is TRUE the container will be auto(by reflections) wired.
     *
     * @param string|array|DefinitionSource|callable|true ...$args
     * @return DI\Container|CompiledContainer
     */
    public static function create(...$args): ContainerInterface
    {
        $last = array_pop($args);

        if (isCallable($last)) {
            $config = $last();
            $config = is_array($config) ? $config : [self::WIRING => $config];
        } else if ($last === DI\WIRING::AUTO) {
            $config = [self::WIRING => DI\WIRING::AUTO];
        } else {
            $last && $args[] = $last;
            $config = [];
        }
        return DI\ContainerConfigurationFactory::create($config, ...$args)->container();
    }
}