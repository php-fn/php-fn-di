<?php
/**
 * Copyright (C) php-fn. See LICENSE file for license details.
 */

namespace php\DI;

use php;
use DI\ContainerBuilder;
use DI\Definition\Source\DefinitionSource;
use Psr\Container\ContainerInterface;

/**
 */
class ContainerConfigurationFactory
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function useContainer(ContainerInterface $container): self
    {
        $this->container = $container;
        return $this;
    }

    /**
     * @param array                            $config
     * @param string|array|DefinitionSource ...$definitions
     *
     * @return ContainerConfiguration
     */
    public static function create(
        array $config = [],
        ...$definitions
    ): ContainerConfiguration {
        return (new static($config))->configure(...$definitions);
    }

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * @param string|array|DefinitionSource ...$definitions
     *
     * @return ContainerConfiguration
     */
    public function configure(...$definitions): ContainerConfiguration
    {
        $builder = new ContainerBuilder(ContainerConfiguration::class);

        $builder->useAutowiring(false)->useAnnotations(false)->ignorePhpDocErrors(false);

        $wiring = $this->config[WIRING] ?? null;
        if (php\hasValue($wiring, [WIRING\REFLECTION, WIRING\AUTO])) {
            $builder->useAutowiring(true);
        } else if ($wiring === WIRING\STRICT) {
            $builder->useAnnotations(true)->ignorePhpDocErrors(false);
        } else if ($wiring === WIRING\TOLERANT) {
            $builder->useAnnotations(true)->ignorePhpDocErrors(true);
        }

        empty($this->config[CACHE]) || $builder->enableDefinitionCache();
        empty($this->config[PROXY]) || $builder->writeProxiesToFile(true, $this->config[PROXY]);
        empty($this->config[COMPILE]) || $builder->enableCompilation($this->config[COMPILE]);
        $this->container && $builder->wrapContainer($this->container);

        foreach ($definitions as $definition) {
            $builder->addDefinitions($definition);
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        $built = $builder->build();
        return $built instanceof ContainerConfiguration
            ? $built
            : new ContainerConfiguration($built);
    }
}
