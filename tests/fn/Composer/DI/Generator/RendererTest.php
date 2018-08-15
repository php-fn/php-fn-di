<?php
/**
 * (c) php-fn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace fn\Composer\DI\Generator;

use fn\test\assert;

class RendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function providerClass(): array
    {
        return [
            'long namespace' => ['cl', 'ns1\ns2', 'ns1\ns2\cl'],
            'short namespace' => ['cl', 'ns1', 'ns1\cl'],
            'no namespace' => ['cl', '', 'cl'],
            'empty' => ['', '', ''],
        ];
    }

    /**
     * @covers       Renderer::getNameSpace
     * @covers       Renderer::getClassName
     *
     * @dataProvider providerClass
     *
     * @param string $expectedClassName
     * @param string $expectedNameSpace
     * @param string $class
     */
    public function testClass(string $expectedClassName, string $expectedNameSpace, string $class)
    {
        $renderer = new Renderer($class);
        assert\same($expectedClassName, $renderer->getClassName());
        assert\same($expectedNameSpace, $renderer->getNameSpace());
    }
}
