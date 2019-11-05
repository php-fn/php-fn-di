<?php
/**
 * Copyright (C) php-fn. See LICENSE file for license details.
 */

namespace php\DI;

use php;
use DI\ContainerBuilder;
use DI\Definition\Source\DefinitionSource;

/**
 */
class ContainerConfigurationFactory
{
    /**
     * @var array
     */
    private $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
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

        $wiring = $this->config[php\DI::WIRING] ?? null;
        if (in_array($wiring, [WIRING::REFLECTION, WIRING::AUTO], true)) {
            $builder->useAutowiring(true);
        } else if ($wiring === WIRING::STRICT) {
            $builder->useAnnotations(true)->ignorePhpDocErrors(false);
        } else if ($wiring === WIRING::TOLERANT) {
            $builder->useAnnotations(true)->ignorePhpDocErrors(true);
        }

        empty($this->config[php\DI::CACHE]) || $builder->enableDefinitionCache();
        empty($this->config[php\DI::PROXY]) || $builder->writeProxiesToFile(true, $this->config[php\DI::PROXY]);
        empty($this->config[php\DI::COMPILE]) || $builder->enableCompilation($this->config[php\DI::COMPILE]);

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
