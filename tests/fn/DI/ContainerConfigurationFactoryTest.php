<?php
/**
 * Copyright (C) php-fn. See LICENSE file for license details.
 */

namespace fn\DI;

use DI\Annotation\Inject;
use DI\CompiledContainer;
use DI\Definition\Source\SourceChain;
use DI\Proxy\ProxyFactory;
use function fn\di;
use fn\test\assert;
use fn\DI\ContainerConfigurationFactory as F;
use PHPUnit\Framework\TestCase;

class ContainerConfigurationFactoryTest extends TestCase
{
    /**
     * @Inject("foo")
     */
    private $foo;

    /**
     * @covers ContainerConfigurationFactory::create
     * @covers ContainerConfigurationFactory::configure
     */
    public function testCreateAndConfigure(): void
    {
        assert\type(ContainerConfiguration::class, $config = F::create([], ['foo' => 'bar']));
        assert\equals(new ProxyFactory, $config->getProxyFactory());
        assert\type(SourceChain::class, $config->getDefinitionSource());
        assert\type(Container::class, $container = $config->container());
        assert\same('bar', $container->get('foo'));
        assert\false($container->has(__CLASS__));

        assert\type(ContainerConfiguration::class, $config = F::create([PROXY => __DIR__], ['foo' => 'bar']));
        assert\equals(new ProxyFactory(true, __DIR__), $config->getProxyFactory());
        assert\type(SourceChain::class, $config->getDefinitionSource());
        assert\type(Container::class, $container = $config->container());
        assert\same('bar', $container->get('foo'));
        assert\false($container->has(__CLASS__));

        assert\type(ContainerConfiguration::class, $config = F::create(
            [COMPILE => sys_get_temp_dir()],
            ['foo' => 'bar']
        ));
        assert\same(null, $config->getProxyFactory());
        assert\type(CompiledContainer::class, $container = $config->container());
        assert\same($container, $config->getDefinitionSource());
        assert\same('bar', $container->get('foo'));
        assert\false($container->has(__CLASS__));

        $this->assertWiring(null, WIRING\AUTO);
        $this->assertWiring(null, WIRING\REFLECTION);
        $this->assertWiring('bar', WIRING\STRICT);
        $this->assertWiring('bar', WIRING\TOLERANT);
    }

    public function testFunctionDi(): void
    {
        assert\type(Container::class, di());
        assert\false(di()->has('foo'));
        assert\same('bar', di(['foo' => 'bar'])->get('foo'));
        assert\false(di()->has(__CLASS__));
        assert\type(__CLASS__, di(WIRING\AUTO)->get(__CLASS__));
        assert\same('bar', di(['foo' => 'bar'], function() {
            return WIRING\STRICT;
        })->get(__CLASS__)->foo);

        $di = di(['foo' => 'bar'], ['bar' => 'foo'], function() {
            return [
                WIRING  => WIRING\NONE,
                COMPILE => sys_get_temp_dir(),
                PROXY   => sys_get_temp_dir(),
            ];
        });
        assert\false($di->has(__CLASS__));
        assert\same('bar', $di->get('foo'));
        assert\same('foo', $di->get('bar'));
    }

    private function assertWiring($expectedFoo, $wiring): void
    {
        assert\type(ContainerConfiguration::class, $config = F::create(
            [WIRING => $wiring],
            ['foo' => 'bar'])
        );
        assert\equals(new ProxyFactory, $config->getProxyFactory());
        assert\type(SourceChain::class, $config->getDefinitionSource());
        assert\type(Container::class, $container = $config->container());
        assert\same('bar', $container->get('foo'));
        assert\true($container->has(__CLASS__));
        assert\equals($expectedFoo, $container->get(__CLASS__)->foo);
    }
}
