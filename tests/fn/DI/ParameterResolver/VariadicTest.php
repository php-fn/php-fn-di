<?php
/**
 * (c) php-fn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace fn\DI\ParameterResolver;

use fn\DI\Invoker;
use Invoker\ParameterResolver\AssociativeArrayResolver;
use fn\test\assert;

class VariadicTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Variadic::__invoke
     */
    public function testInvoke()
    {
        $invoker  = new Invoker(new Variadic, new AssociativeArrayResolver);
        $callable = function($p1, ...$p2) {
            return \func_get_args();
        };

        assert\same(
            ['P1', 'P21', 'P22'],
            $invoker->call($callable, ['p1' => 'P1', 'p2' => ['P21', 'P22']])
        );

        assert\same(
            ['P1', 'P2'],
            $invoker->call($callable, ['p1' => 'P1', 'p2' => 'P2'])
        );
    }
}
