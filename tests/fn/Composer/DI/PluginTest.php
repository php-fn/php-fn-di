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
            'extra-empty'  => ['expected' => Invoker::class, 'config' => ['extra' => []]],
            'extra-string' => ['expected' => 'di.php', 'config' => ['extra' => ['di' => 'config/di.php']]],
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
        (new Composer\Util\ProcessExecutor)->execute('php test.php', $output, $cwd);

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
