<?php
/**
 * (c) php-fn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace fn\DI;

use DI\Definition\Source\MutableDefinitionSource;
use DI\Proxy\ProxyFactory;
use Psr\Container\ContainerInterface;

class ContainerConfiguration
{
    /**
     * @var MutableDefinitionSource
     */
    private $definitionSource;
    /**
     * @var ProxyFactory
     */
    private $proxyFactory;
    /**
     * @var null|ContainerInterface
     */
    private $wrapperContainer;

    /**
     * @param MutableDefinitionSource $definitionSource
     * @param ProxyFactory            $proxyFactory
     * @param ContainerInterface|null $wrapperContainer
     */
    public function __construct(
        MutableDefinitionSource $definitionSource,
        ProxyFactory $proxyFactory,
        ContainerInterface $wrapperContainer = null
    ) {
        $this->definitionSource = $definitionSource;
        $this->proxyFactory = $proxyFactory;
        $this->wrapperContainer = $wrapperContainer;
    }

    /**
     * @return MutableDefinitionSource
     */
    public function getDefinitionSource(): MutableDefinitionSource
    {
        return $this->definitionSource;
    }

    /**
     * @return ProxyFactory
     */
    public function getProxyFactory(): ProxyFactory
    {
        return $this->proxyFactory;
    }

    /**
     * @return null|ContainerInterface
     */
    public function getWrapperContainer()
    {
        return $this->wrapperContainer;
    }

    /**
     * @param string $containerClass
     *
     * @return \DI\Container
     */
    public function createContainer(string $containerClass): \DI\Container
    {
        return new $containerClass($this->getDefinitionSource(), $this->getProxyFactory(), $this->getProxyFactory());
    }
}
