<?php
/**
 * (c) php-fn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace fn\DI;

use function DI\value;
use fn;
use fn\test\assert;
use Invoker\ParameterResolver\DefaultValueResolver;

class InvokerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Invoker::resolve
     */
    public function testResolve()
    {
        assert\exception('argument $candidate is not callable', function() {
            (new Invoker)->resolve('count');
        });

        assert\same($func = function() {}, (new Invoker)->resolve($func));
        assert\same([$this, __FUNCTION__], (new Invoker)->resolve([$this, __FUNCTION__]));

        $resolver = new Invoker(fn\di()->container(['callback' => value($func)]));
        assert\same($func, $resolver->resolve('callback'));
    }

    /**
     * @covers Invoker::reflect
     */
    public function testReflect()
    {
        $resolver = $this->resolver(['callback' => value(function(string $s1){})]);
        assert\type(\ReflectionFunction::class, $resolver->reflect('callback'));
        assert\type(\ReflectionMethod::class, $resolver->reflect([$this, __FUNCTION__]));
    }

    /**
     * @covers Invoker::parameters
     */
    public function testParameters()
    {
        $resolver = $this->resolver([\PHPUnit_Framework_TestCase::class => $this, static::class => $this]);
        $callback = function(
            string $p1,
            self $p2,
            bool $p3 = false,
            \PHPUnit_Framework_TestCase $p4 = null
        ) {};

        assert\equals([1 => $this, 3 => $this, 2 => false], $resolver->parameters($callback));
        assert\equals(
            [1 => $this, 3 => $this, 2 => true, 0 => false],
            $resolver->parameters($callback, ['P3' => true, 'p1' => false])
        );
    }

    /**
     * @covers Invoker::call
     */
    public function testCall()
    {
        assert\same(
            [$this, true, 'value'],
            $this->resolver([static::class => $this])->call(
                function(self $test, $default = true, $provided = null) {
                    return [$test, $default, $provided];
                },
                ['provided' => 'value']
            )
        );
    }

    private function resolver(array $definition = []): Invoker
    {
        return new Invoker(
            fn\di()->container($definition),
            function(\ReflectionParameter $parameter, array $provided) {
                $map = fn\map(array_change_key_case($provided));
                if (($value = $map->get(strtolower($parameter->getName()), null)) !== null) {
                    yield $value;
                }
            },
            new DefaultValueResolver
        );
    }
}
