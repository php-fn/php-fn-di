<?php
/**
 * (c) php-fn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace fn\DI;

use Invoker\Reflection\CallableReflection;
use ReflectionParameter;
use fn;
use fn\test\assert;

class ResolverCallbackTest extends \PHPUnit_Framework_TestCase
{
    public function providerGetParameters(): array
    {
        return [
            'empty' => [[[], []], function() {}],
            'arguments' => [[['a1', 'a2', 'a3'], ['provided']], function($a1, $a2, array ...$a3) {}, ['provided']],
            'resolved' => [[['a1', 'a3'], []], function($a1, $a2, $a3) {}, [], [1 => null]],
        ];
    }

    /**
     * @dataProvider providerGetParameters
     * @covers       ResolverCallback::getParameters
     * @param array $expected
     * @param callable $callable
     * @param array $provided
     * @param array $resolved
     */
    public function testGetParameters(array $expected, callable $callable, array $provided = [], array $resolved = [])
    {
        $resolver = new ResolverCallback(function(array $provided, array $resolved, ReflectionParameter ...$parameters) {
            return [
                fn\traverse($parameters, function(ReflectionParameter $parameter) {
                    return $parameter->getName();
                }),
                $provided
            ];
        });
        assert\same($expected, $resolver->getParameters(CallableReflection::create($callable), $provided, $resolved));
    }
}
