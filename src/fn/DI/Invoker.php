<?php
/**
 * (c) php-fn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/** @noinspection PhpDocMissingThrowsInspection */

namespace fn\DI;

use fn;
use fn\DI\ParameterResolver\Callback;

use Invoker\{
    InvokerInterface,
    ParameterResolver,
    Reflection\CallableReflection
};
use Psr\Container\ContainerInterface;
use ReflectionFunctionAbstract;

/**
 */
class Invoker extends ParameterResolver\ResolverChain implements InvokerInterface
{
    /**
     * @var \Invoker\Invoker
     */
    private $invoker;

    /**
     * @param mixed ...$resolvers
     */
    public function __construct(...$resolvers)
    {
        $this->invoker = new \Invoker\Invoker($this);

        parent::__construct(fn\traverse($resolvers, function($candidate): ParameterResolver\ParameterResolver {
            if ($candidate instanceof ParameterResolver\ParameterResolver) {
                return $candidate;
            }
            if ($candidate instanceof ContainerInterface) {
                $this->invoker = new \Invoker\Invoker($this, $candidate);
                return new ParameterResolver\Container\TypeHintContainerResolver($candidate);
            }
            return new Callback($candidate);
        }));
    }

    /**
     * @param callable $candidate
     *
     * @return callable
     */
    public function resolve($candidate)
    {
        if ($resolver = $this->invoker->getCallableResolver()) {
            /** @noinspection PhpUnhandledExceptionInspection */
            return $resolver->resolve($candidate);
        }
        fn\isCallable($candidate, true) || fn\fail('argument $candidate is not callable');
        return $candidate;
    }

    /**
     * @param callable $candidate
     *
     * @return ReflectionFunctionAbstract
     */
    public function reflect($candidate): ReflectionFunctionAbstract
    {
        return CallableReflection::create($this->resolve($candidate));
    }

    /**
     * @param callable $candidate
     * @param array $provided
     *
     * @return array
     */
    public function parameters($candidate, array $provided = []): array
    {
        return $this->getParameters($this->reflect($candidate), $provided, []);
    }

    /**
     * @inheritdoc
     */
    public function call($callable, array $parameters = [])
    {
        return $this->invoker->call($callable, $parameters);
    }
}
