<?php
/**
 * Copyright (C) php-fn. See LICENSE file for license details.
 */

namespace php\DI;

use DI\Annotation\Inject;
use DI\CompiledContainer;
use DI\Definition\Source\SourceChain;
use DI\Proxy\ProxyFactory;
use php\test\assert;
use php\DI\ContainerConfigurationFactory as F;
use PHPUnit\Framework\TestCase;
use php\DI;

/**
 * @coversDefaultClass ContainerConfigurationFactory
 */
class ContainerConfigurationFactoryTest extends TestCase
{
    /**
     * @Inject("foo")
     */
    private $foo;

    /**
     * @covers \php\DI\ContainerConfigurationFactory::create
     * @covers \php\DI\ContainerConfigurationFactory::configure
     */
    public function testCreateAndConfigure(): void
    {
        assert\type(ContainerConfiguration::class, $config = F::create([], ['foo' => 'bar']));
        assert\equals(new ProxyFactory, $config->getProxyFactory());
        assert\type(SourceChain::class, $config->getDefinitionSource());
        assert\type(Container::class, $container = $config->container());
        assert\same('bar', $container->get('foo'));
        assert\false($container->has(__CLASS__));

        assert\type(ContainerConfiguration::class, $config = F::create([DI::PROXY => __DIR__], ['foo' => 'bar']));
        assert\equals(new ProxyFactory(true, __DIR__), $config->getProxyFactory());
        assert\type(SourceChain::class, $config->getDefinitionSource());
        assert\type(Container::class, $container = $config->container());
        assert\same('bar', $container->get('foo'));
        assert\false($container->has(__CLASS__));

        assert\type(ContainerConfiguration::class, $config = F::create(
            [DI::COMPILE => sys_get_temp_dir()],
            ['foo' => 'bar']
        ));
        assert\same(null, $config->getProxyFactory());
        assert\type(CompiledContainer::class, $container = $config->container());
        assert\same($container, $config->getDefinitionSource());
        assert\same('bar', $container->get('foo'));
        assert\false($container->has(__CLASS__));

        $this->assertWiring(null, WIRING::AUTO);
        $this->assertWiring(null, WIRING::REFLECTION);
        $this->assertWiring('bar', WIRING::STRICT);
        $this->assertWiring('bar', WIRING::TOLERANT);
    }

    /**
     * @uses \php\di
     */
    public function testFunctionDi(): void
    {
        assert\type(Container::class, DI::create());
        assert\false(DI::create()->has('foo'));
        assert\same('bar', DI::create(['foo' => 'bar'])->get('foo'));
        assert\false(DI::create()->has(__CLASS__));
        assert\type(__CLASS__, DI::create(WIRING::AUTO)->get(__CLASS__));
        assert\same('bar', DI::create(['foo' => 'bar'], function () {
            return WIRING::STRICT;
        })->get(__CLASS__)->foo);

        $di = DI::create(['foo' => 'bar'], ['bar' => 'foo'], function () {
            return [
                DI::WIRING => WIRING::NONE,
                DI::COMPILE => sys_get_temp_dir(),
                DI::PROXY => sys_get_temp_dir(),
            ];
        });
        assert\false($di->has(__CLASS__));
        assert\same('bar', $di->get('foo'));
        assert\same('foo', $di->get('bar'));
    }

    private function assertWiring($expectedFoo, $wiring): void
    {
        assert\type(ContainerConfiguration::class, $config = F::create(
            [DI::WIRING => $wiring],
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
