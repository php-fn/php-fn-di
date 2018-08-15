<?php
/**
 * (c) php-fn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace fn\Composer\DI;

use fn\test\assert, Composer;

/**
 */
class PluginTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        $fs = new Composer\Util\Filesystem;
        \file_exists($dir = self::target()) && $fs->removeDirectoryPhp($dir);
        $fs->ensureDirectoryExists($dir);
    }

    private static function target(string ...$path): string
    {
        return \dirname(__DIR__, 4) . '/tmp/' . implode('/', $path);
    }

    public static function providerOnAutoloadDump(): array
    {
        return [
            'extra-empty'  => [Invoker::class, ['extra' => []]],
            'extra-string' => ['di.php', ['extra' => ['di' => 'config/di.php']]],
            'extra-string-reflection' => [
                \DI\ContainerBuilder::class,
                [
                    'extra' => [
                        'di'        => 'config/di.php',
                        'di-config' => ['wiring' => ContainerConfigurationFactory::WIRING_REFLECTION]
                    ]
                ]
            ],
            'extra-array' => [
                \json_encode([
                    'invoker-value' => 'foo',
                    'c2-file' => 'C2',
                    'c31-file' => 'C31',
                    'c32-file' => 'C32',
                    'c3-value' => ['foo' => ['a', 'b']],
                    'c4-file' => 'C4',
                    'c5-file' => 'C5',
                ], JSON_PRETTY_PRINT),
                [
                    'extra' => [
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
                        'di-config' => [
                            'wiring' => 'reflection',
                            '@ns\c5' => 'cast-to-array',
                            '@ns\c1' => ['cache' => true],
                            '@ns\c2' => ['wiring' => false],
                        ],
                    ]
                ]
            ],
        ];
    }

    /**
     * @dataProvider providerOnAutoloadDump
     *
     * @covers       Plugin::onAutoloadDump
     *
     * @param mixed $expected
     * @param array $config
     */
    public function testOnAutoloadDump($expected, array $config)
    {
        (new Composer\Util\Filesystem)->copy(
            \dirname(__DIR__, 3) . "/fixtures/{$this->dataDescription()}",
            self::target($this->dataDescription())
        );
        $cwd = \dirname($this->jsonFile($config));

        (new Composer\Util\ProcessExecutor)->execute('composer install -q', $output = '', $cwd);
        (new Composer\Util\ProcessExecutor)->execute('php -d apc.enable_cli=1 test.php', $output, $cwd);

        assert\equals($expected, $output);
    }

    private function jsonFile(array $config): string
    {
        $jsonFile = self::target($this->dataDescription(), 'composer.json');

        /** @noinspection PhpUnhandledExceptionInspection */
        (new Composer\Json\JsonFile($jsonFile))->write($config + [
            'config'       => ['cache-dir' => '/dev/null', 'data-dir' => '/dev/null'],
            'repositories' => [['type' => 'git', 'url' => \dirname(__DIR__, 4)]],
            'require'      => ['php-fn/di' => 'dev-master'],
        ]);

        return $jsonFile;
    }
}
