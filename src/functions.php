<?php
/**
 * Copyright (C) php-fn. See LICENSE file for license details.
 */

namespace fn
{
    use DI\CompiledContainer;
    use DI\Definition\Source\DefinitionSource;
    use Psr\Container\ContainerInterface;

    /**
     * Create a container from the given definitions.
     * If the last parameter is a callable it will be invoked to get the container configuration.
     * If the last parameter is TRUE the container will be auto(by reflections) wired.
     *
     * @param string|array|DefinitionSource|callable|true ...$args
     * @return DI\Container|CompiledContainer
     */
    function di(...$args): ContainerInterface
    {
        $last = array_pop($args);

        if (isCallable($last)) {
            $config = $last();
            $config = is_array($config) ? $config : [DI\WIRING => $config];
        } else if ($last === DI\WIRING\AUTO) {
            $config = [DI\WIRING => DI\WIRING\AUTO];
        } else {
            $last && $args[] = $last;
            $config = [];
        }
        return DI\ContainerConfigurationFactory::create($config, ...$args)->container();
    }
}

namespace fn\Composer
{
    /**
     * @return DI|\fn\DI\Container
     */
    function di(): DI
    {
        return DIClassLoader::instance()->getContainer();
    }
}
