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
                'expected' => [
                    '@' . Invoker::class => [
                        'config' => [],
                        'files' => [],
                        'containers' => [],
                        'arrays' => [],
                    ],
                ],
                'di' => [],
                'config' => []
            ],
            'complex' => [
                'expected' => [
                    '@' . Invoker::class => [
                        'config' => ['wiring' => 'reflection'],
                        'files' => [],
                        'containers' => ['@ns\c1', '@ns\c5'],
                        'arrays' => ['foo' => 'bar', 'bar' => 'foo', 'baz' => ['foo', 'bar']],
                    ],
                    '@ns\c1' => [
                        'config' => ['cache' => true, 'wiring' => 'reflection'],
                        'files' => [],
                        'containers' => ['@ns\c2', '@ns\c3'],
                        'arrays' => [],
                    ],
                    '@ns\c2' => [
                        'config' => ['wiring' => false],
                        'files' => ['config/c2.php'],
                        'containers' => [],
                        'arrays' => [],
                    ],
                    '@ns\c3' => [
                        'config' => ['wiring' => 'reflection'],
                        'files' => ['config/c31.php', 'config/c32.php'],
                        'containers' => ['@ns\c4'],
                        'arrays' => ['foo' => 'bar', 'bar' => ['foo' => ['a', 'b']]],
                    ],
                    '@ns\c4' => [
                        'config' => ['wiring' => 'reflection'],
                        'files' => ['config/c4.php'],
                        'containers' => [],
                        'arrays' => [],
                    ],
                    '@ns\c5' => [
                        'config' => ['cast-to-array', 'wiring' => 'reflection'],
                        'files' => ['config/c5.php'],
                        'containers' => ['@ns\c4'],
                        'arrays' => [],
                    ],
                ],
                'di' => [
                    'foo' => 'bar',
                    '@ns\\c1' => [
                        '@ns\\c2' => 'config/c2.php',
                        '@ns\\c3' => [
                            'config/c31.php',
                            'foo' => 'bar',
                            'config/c32.php',
                            '@ns\\c4' => 'config/c4.php',
                            'bar' => [
                                'foo' => ['a', 'b']
                            ],
                        ],
                    ],
                    'bar' => 'foo',
                    '@ns\\c5' => [
                        '@ns\\c4',
                        'config/c5.php',
                    ],
                    'baz' => ['foo', 'bar'],
                ],
                'config' => [
                    'wiring'  => 'reflection',
                    '@ns\\c5' => 'cast-to-array',
                    '@ns\\c1' => ['cache' => true],
                    '@ns\\c2' => ['wiring' => false],
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
        $actual = \iterator_to_array(new Provider($di, $config), true);
        assert\equals($expected, $actual);
    }
}
