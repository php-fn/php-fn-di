<?php
/**
 * (c) php-fn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace fn\DI;

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
     * @param array $config
     * @param mixed ...$definitions
     *
     * @return ContainerConfiguration
     */
    public static function create(
        array $config,
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

        switch ($this->config[WIRING] ?? null) {
            case WIRING\REFLECTION:
                $builder->useAutowiring(true);
                break;
            case WIRING\TOLERANT:
                $builder->useAnnotations(true)->ignorePhpDocErrors(true);
                break;
            case WIRING\STRICT:
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

    /**
     * @param string|array|DefinitionSource ...$definitions
     *
     * @return Container
     */
    public function container(...$definitions): Container
    {
        return $this->configure(...$definitions)->container();
    }
}
