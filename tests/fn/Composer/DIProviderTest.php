<?php
/**
 * Copyright (C) php-fn. See LICENSE file for license details.
 */

namespace fn\Composer;

use fn;
use fn\test\assert;

/**
 */
class DIProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return array[]
     */
    public function providerGetIterator(): array
    {
        return [
            'empty' => [
                'expected' => [new DIRenderer(DI::class, [], [], [], [], true)],
                'di' => [],
                'config' => []
            ],
            'complex' => [
                'expected' => [
                    new DIRenderer(
                        DI::class,
                        [fn\DI\WIRING => fn\DI\WIRING\REFLECTION,],
                        ['ns\c1', 'ns\c5'],
                        [],
                        ['foo' => 'bar', 'bar' => 'foo', 'baz' => ['foo', 'bar']],
                        true
                    ),
                    new DIRenderer('ns\c1', ['cache' => true, fn\DI\WIRING => fn\DI\WIRING\REFLECTION,], ['ns\c2', 'ns\c3']),
                    new DIRenderer('ns\c2', [fn\DI\WIRING => false], [], ['config/c2.php']),
                    new DIRenderer(
                        'ns\c3',
                        [fn\DI\WIRING => fn\DI\WIRING\REFLECTION,],
                        ['ns\c4'],
                        ['config/c31.php', 'config/c32.php'],
                        ['foo' => 'bar', 'bar' => ['foo' => ['a', 'b']]]
                    ),
                    new DIRenderer('ns\c4', [fn\DI\WIRING => fn\DI\WIRING\REFLECTION,], [], ['config/c4.php']),
                    new DIRenderer(
                        'ns\c5',
                        ['cast-to-array', fn\DI\WIRING => fn\DI\WIRING\REFLECTION,],
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
                    fn\DI\WIRING => fn\DI\WIRING\REFLECTION,
                    '@ns\c5' => 'cast-to-array',
                    '@ns\c1' => ['cache' => true],
                    '@ns\c2' => [fn\DI\WIRING => false],
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
        foreach (new DIProvider($di, $config) as $renderer) {
            $actual[] = $renderer;
        }
        assert\equals($expected, $actual);
    }
}
