<?php
/**
 * (c) php-fn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace fn\Composer\DI;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

/**
 */
class ContainerConfigurationFactory
{
    const WIRING_REFLECTION = 'reflection';
    const WIRING_STRICT = 'strict';
    const WIRING_TOLERANT = 'tolerant';

    /**
     * @var array
     */
    private $config;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param array                   $config
     * @param ContainerInterface|null $container
     */
    public function __construct(array $config, ContainerInterface $container = null)
    {
        $this->config    = $config;
        $this->container = $container;
    }

    /**
     * @param array                   $config
     * @param ContainerInterface|null $container
     * @param mixed                   ...$definitions
     *
     * @return ContainerConfiguration
     */
    public static function create(
        array $config,
        ContainerInterface $container = null,
        ...$definitions
    ): ContainerConfiguration {
        return (new static($config, $container))->configure(...$definitions);
    }

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * @param mixed ...$definitions
     *
     * @return ContainerConfiguration
     */
    public function configure(...$definitions): ContainerConfiguration
    {
        $builder = new ContainerBuilder(ContainerConfiguration::class);

        $builder->useAutowiring(false)->useAnnotations(false)->ignorePhpDocErrors(false);

        switch ($this->config['wiring'] ?? null) {
            case self::WIRING_REFLECTION:
                $builder->useAutowiring(true);
                break;
            case self::WIRING_TOLERANT:
                $builder->useAnnotations(true)->ignorePhpDocErrors(true);
                break;
            case self::WIRING_STRICT:
                $builder->useAnnotations(true)->ignorePhpDocErrors(false);
                break;
        }

        empty($this->config['cache']) || $builder->enableDefinitionCache();
        empty($this->config['proxy']) || $builder->writeProxiesToFile(true, $this->config['proxy']);
        empty($this->config['compile']) || $builder->enableCompilation($this->config['compile']);
        $this->container && $builder->wrapContainer($this->container);

        foreach ($definitions as $definition) {
            $builder->addDefinitions($definition);
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $builder->build();
    }
}
