<?php
/**
 * Copyright (C) php-fn. See LICENSE file for license details.
 */

namespace fn\DI;

use function DI\value;
use fn;
use fn\test\assert;
use Invoker\ParameterResolver\DefaultValueResolver;

class InvokerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Invoker::resolve
     */
    public function testResolve(): void
    {
        assert\exception('argument $candidate is not callable', function() {
            (new Invoker)->resolve('count');
        });

        assert\same($func = function() {}, (new Invoker)->resolve($func));
        assert\same([$this, __FUNCTION__], (new Invoker)->resolve([$this, __FUNCTION__]));

        $resolver = new Invoker(fn\di(['callback' => value($func)]));
        assert\same($func, $resolver->resolve('callback'));
    }

    /**
     * @covers Invoker::reflect
     */
    public function testReflect(): void
    {
        $resolver = $this->resolver(['callback' => value(function(string $s1){})]);
        assert\type(\ReflectionFunction::class, $resolver->reflect('callback'));
        assert\type(\ReflectionMethod::class, $resolver->reflect([$this, __FUNCTION__]));
    }

    /**
     * @covers Invoker::parameters
     */
    public function testParameters(): void
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
    public function testCall(): void
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
            fn\di($definition),
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
