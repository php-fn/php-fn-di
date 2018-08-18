<?php
/**
 * (c) php-fn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace fn\DI;

use DI\Definition\Definition;
use DI\Definition\Source\DefinitionArray;
use DI\Definition\Source\MutableDefinitionSource;
use DI\Definition\Source\ReflectionBasedAutowiring;
use DI\Definition\Source\SourceChain;
use DI\Proxy\ProxyFactory;
use Psr\Container\ContainerInterface;

class Container extends \DI\Container implements MutableDefinitionSource
{
    use DefinitionSourceProxyTrait;

    /**
     * @inheritdoc
     */
    public function __construct(
        MutableDefinitionSource $definitionSource = null,
        ProxyFactory $proxyFactory = null,
        ContainerInterface $wrapperContainer = null
    ) {
        if (!$definitionSource) {
            /** @noinspection PhpUnhandledExceptionInspection */
            ($definitionSource = new SourceChain([new ReflectionBasedAutowiring]))->setMutableDefinitionSource(
                new DefinitionArray([], new ReflectionBasedAutowiring)
            );
        }
        parent::__construct($this->definitionSource = $definitionSource, $proxyFactory, $wrapperContainer);
    }

    /**
     * @inheritdoc
     */
    public function addDefinition(Definition $definition)
    {
        $this->setDefinition($definition->getName(), $definition);
    }
}
