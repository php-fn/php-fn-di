<?php
/**
 * (c) php-fn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace fn;

use DI\Definition\Source\DefinitionSource;

/**
 * @param string|array|DefinitionSource $definition
 * @param array                         $config
 * @return DI\Container
 */
function di($definition = [], array $config = [])
{
    return DI\ContainerConfigurationFactory::create($config, ...[$definition])->container();
}
