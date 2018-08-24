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
            'empty' => [[], function() {}],
            'no match' => [[], function() {}, ['a1' => 'A1']],
            'partial' => [
                [1 => 'A2', 2 => [[]]],
                function($a1, $a2, array ...$a3) {}, ['a3' => [[]], 'a2' => 'A2']
            ],
            'resolved' => [
                [2 => 'resolved', 1 => 'A2'],
                function($a1, $a2, $a3) {}, ['a3' => 'A3', 'a2' => 'A2'],
                [2 => 'resolved']
            ],
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
        $resolver = new ResolverCallback(function(array $provided, ReflectionParameter ...$parameters) {
            $map = fn\map($provided);
            return fn\traverse($parameters, function(ReflectionParameter $parameter, &$key) use($map) {
                $key = $parameter->getPosition();
                return $map->get($parameter->getName(), null);
            });
        });
        assert\same($expected, $resolver->getParameters(CallableReflection::create($callable), $provided, $resolved));
    }
}
