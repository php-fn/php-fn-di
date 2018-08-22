<?php
/**
 * (c) php-fn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace fn\DI;

use fn;
use Invoker\Invoker;
use Invoker\InvokerInterface;
use Invoker\ParameterResolver;
use Invoker\Reflection\CallableReflection;
use Psr\Container\ContainerInterface;

/**
 */
class ResolverChain extends ParameterResolver\ResolverChain implements InvokerInterface
{
    /**
     * @var Invoker
     */
    private $invoker;

    /**
     * @param mixed ...$resolvers
     */
    public function __construct(...$resolvers)
    {
        $this->invoker = new Invoker($this);

        parent::__construct(fn\traverse($resolvers, function($candidate): ParameterResolver\ParameterResolver {
            if ($candidate instanceof ParameterResolver\ParameterResolver) {
                return $candidate;
            }
            if ($candidate instanceof ContainerInterface) {
                $this->invoker = new Invoker($this, $candidate);
                return new ParameterResolver\Container\TypeHintContainerResolver($candidate);
            }
            return new ResolverCallback($candidate);
        }));
    }

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * @param callable $callable
     * @param array    $provided
     *
     * @return array
     */
    public function resolve($callable, array $provided = []): array
    {
        if ($resolver = $this->invoker->getCallableResolver()) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $callable = $resolver->resolve($callable);
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->getParameters(CallableReflection::create($callable), $provided, []);
    }

    /**
     * @inheritdoc
     */
    public function call($callable, array $parameters = [])
    {
        return $this->invoker->call($callable, $parameters);
    }
}
