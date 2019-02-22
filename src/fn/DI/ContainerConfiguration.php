<?php
/**
 * (c) php-fn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace fn\DI;

use DI\CompiledContainer;
use DI\Definition\Source\MutableDefinitionSource;
use DI\Proxy\ProxyFactory;
use Psr\Container\ContainerInterface;

class ContainerConfiguration
{
    /**
     * @var MutableDefinitionSource|ContainerInterface
     */
    private $definitionSource;

    /**
     * @var ProxyFactory
     */
    private $proxyFactory;

    /**
     * @var ContainerInterface
     */
    private $wrapperContainer;

    /**
     * @param MutableDefinitionSource|ContainerInterface $definitionSource
     * @param ProxyFactory                               $proxyFactory
     * @param ContainerInterface                         $wrapperContainer
     */
    public function __construct(
        $definitionSource = null,
        ProxyFactory $proxyFactory = null,
        ContainerInterface $wrapperContainer = null
    ) {
        $this->definitionSource = $definitionSource;
        $this->proxyFactory     = $proxyFactory;
        $this->wrapperContainer = $wrapperContainer;
    }

    /**
     * @return MutableDefinitionSource|ContainerInterface
     */
    public function getDefinitionSource()
    {
        return $this->definitionSource;
    }

    /**
     * @return ProxyFactory
     */
    public function getProxyFactory(): ?ProxyFactory
    {
        return $this->proxyFactory;
    }

    /**
     * @return ContainerInterface
     */
    public function getWrapperContainer(): ?ContainerInterface
    {
        return $this->wrapperContainer;
    }

    /**
     * @param string $containerClass
     *
     * @return ContainerInterface|Container|CompiledContainer
     */
    public function container(string $containerClass = Container::class): ContainerInterface
    {
        if (($source = $this->getDefinitionSource()) instanceof MutableDefinitionSource) {
            return new $containerClass($source, $this->getProxyFactory(), $this->getWrapperContainer());
        }
        return $source;
    }
}
