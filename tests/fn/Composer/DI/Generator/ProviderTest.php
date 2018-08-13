<?php
/**
 * (c) php-fn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace fn\Composer\DI\Generator;

use fn\Composer\DI\Invoker;
use fn\test\assert;

/**
 */
class ProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array[]
     */
    public function providerGetIterator(): array
    {
        return [
            'empty' => [
                'expected' => [new Renderer(Invoker::class)],
                'di' => [],
                'config' => []
            ],
            'complex' => [
                'expected' => [
                    new Renderer(
                        Invoker::class,
                        ['wiring' => 'reflection'],
                        ['ns\c1', 'ns\c5'],
                        [],
                        ['foo' => 'bar', 'bar' => 'foo', 'baz' => ['foo', 'bar']]
                    ),
                    new Renderer('ns\c1', ['cache' => true, 'wiring' => 'reflection'], ['ns\c2', 'ns\c3']),
                    new Renderer('ns\c2', ['wiring' => false], [], ['config/c2.php']),
                    new Renderer(
                        'ns\c3',
                        ['wiring' => 'reflection'],
                        ['ns\c4'],
                        ['config/c31.php', 'config/c32.php'],
                        ['foo' => 'bar', 'bar' => ['foo' => ['a', 'b']]]
                    ),
                    new Renderer('ns\c4', ['wiring' => 'reflection'], [], ['config/c4.php']),
                    new Renderer(
                        'ns\c5',
                        ['cast-to-array', 'wiring' => 'reflection'],
                        ['ns\c4'],
                        ['config/c5.php']
                    )
                ],
                'di' => [
                    'foo' => 'bar',
                    '@ns\c1' => [
                        '@ns\c2' => 'config/c2.php',
                        '@ns\c3' => [
                            'config/c31.php',
                            'foo' => 'bar',
                            'config/c32.php',
                            '@ns\c4' => 'config/c4.php',
                            'bar' => [
                                'foo' => ['a', 'b']
                            ],
                        ],
                    ],
                    'bar' => 'foo',
                    '@ns\c5' => [
                        '@ns\c4',
                        'config/c5.php',
                    ],
                    'baz' => ['foo', 'bar'],
                ],
                'config' => [
                    'wiring' => 'reflection',
                    '@ns\c5' => 'cast-to-array',
                    '@ns\c1' => ['cache' => true],
                    '@ns\c2' => ['wiring' => false],
                ]
            ],
        ];
    }

    /**
     * @dataProvider providerGetIterator
     *
     * @param array $expected
     * @param array $di
     * @param array $config
     */
    public function testGetIterator(array $expected, array $di, array $config)
    {
        $actual = [];
        foreach (new Provider($di, $config) as $renderer) {
            $actual[] = $renderer;
        }
        assert\equals($expected, $actual);
    }
}
