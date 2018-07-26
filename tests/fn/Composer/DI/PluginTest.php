<?php
/**
 * (c) php-fn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace fn\Composer\DI;

use Composer;

/**
 */
class PluginTest extends \PHPUnit_Framework_TestCase
{
    public static function providerOnAutoloadDump(): array
    {
        return [
            'empty'  => ['expected' => '', 'config' => [
                'extra' => [],
            ]],
        ];
    }

    /**
     * @dataProvider providerOnAutoloadDump
     *
     * @covers       Plugin::onAutoloadDump
     *
     * @param mixed $expected
     * @param array $extra
     */
    public function testOnAutoloadDump($expected, array $extra)
    {
        $factory = new Composer\Factory;

        $composer = $factory->createComposer(
            $io = new Composer\IO\NullIO,
            $jsonFile = $this->config($extra),
            false,
            \dirname($jsonFile)
        );

        $composer->getPluginManager()->addPlugin(new Plugin);
        $installer = Composer\Installer::create($io, $composer);
        $installer->run();
    }

    private function config(array $config): string
    {
        $jsonFile = self::target($this->dataDescription(), 'composer.json');
        /** @noinspection PhpUnhandledExceptionInspection */
        (new Composer\Json\JsonFile($jsonFile))->write(
            $config + [
                'repositories' => [
                    ['type'    => 'package',
                     'package' => [
                         'name'    => 'php-fn/di',
                         'version' => 'dev-master',
                         'dist'    => ['type' => 'tar', 'url' => self::package()],
                     ],
                    ],
                ],
                'require'      => [
                    'php-fn/di' => 'dev-master',
                ],
            ]
        );

        return $jsonFile;
    }

    private static function target(string ...$path): string
    {
        return \dirname(__DIR__, 4) . '/tmp/' . implode('/', $path);
    }

    private static function package(): string
    {
        static $package;
        if ($package) {
            return $package;
        }

        $fs = new Composer\Util\Filesystem;

        \file_exists($dir = self::target()) && $fs->removeDirectoryPhp($dir);
        $fs->ensureDirectoryExists($dir);

        return $package = (new Composer\Package\Archiver\PharArchiver)->archive(
            \dirname(__DIR__, 4),
            $dir . '/php-fn-di.tar',
            'tar',
            ['.idea', 'composer.lock']
        );
    }
}
