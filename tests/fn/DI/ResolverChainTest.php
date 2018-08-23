<?php
/**
 * (c) php-fn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace fn\DI;

use function DI\value;
use fn;
use fn\test\assert;

class ResolverChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ResolverChain::resolve
     */
    public function testResolve()
    {
        assert\exception('argument $candidate is not callable', function() {
            (new ResolverChain)->resolve('count');
        });

        assert\same($func = function() {}, (new ResolverChain)->resolve($func));
        assert\same([$this, __FUNCTION__], (new ResolverChain)->resolve([$this, __FUNCTION__]));

        $resolver = new ResolverChain(fn\di(['callback' => value($func)]));
        assert\same($func, $resolver->resolve('callback'));
    }
}
