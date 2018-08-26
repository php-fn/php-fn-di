<?php
/**
 * (c) php-fn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace fn\DI\ParameterResolver;

use ReflectionParameter;

/**
 */
class Variadic extends Callback
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct($this);
    }

    /**
     * @param array               $provided
     * @param ReflectionParameter ...$parameters
     * @return array
     */
    public function __invoke(array $provided, ReflectionParameter ...$parameters)
    {
        foreach ($parameters as $parameter) {
            if ($parameter->isVariadic() && array_key_exists($parameter->getName(), $provided)) {
                $resolved = [];
                foreach (array_values((array)$provided[$parameter->getName()]) as $i => $value) {
                    $resolved[$parameter->getPosition() + $i] = $value;
                }
                return $resolved;
            }
        }
        return [];
    }
}
