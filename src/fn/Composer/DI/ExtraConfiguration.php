<?php
/**
 * (c) php-fn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace fn\Composer\DI;

use ArrayIterator;
use IteratorAggregate;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

/**
 */
class ExtraConfiguration implements IteratorAggregate
{
    /**
     * @var array[]
     */
    private $di = [];

    /**
     * @var array
     */
    private $defaultConfig = [];

    /**
     * @var array[]
     */
    private $containerConfig = [];

    /**
     * @param array $di
     * @param array $config
     */
    public function __construct(array $di, array $config = [])
    {
        foreach ($config as $key => $value) {
            if (static::isClass($key)) {
                $this->containerConfig[$key] = (array)$value;
            } else {
                $this->defaultConfig[$key] = $value;
            }
        }
        $this->init($di);
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->di);
    }

    /**
     * @param array $di
     */
    private function init(array $di)
    {
        $it = new RecursiveIteratorIterator(
            new RecursiveArrayIterator($di),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $class = '@' . Invoker::class;
        $this->di = [$class => [
            'config' => $this->config($class),
            'files' => [],
            'containers' => [],
            'arrays' => [],
        ]];
        $parents = [-1 => $class];

        foreach ($it as $key => $value) {
            $depth = $it->getDepth();
            $parents[$depth] = $key;
            $parents = \array_slice($parents, 0, $depth + 2, true);
            $parent = $parents[$depth - 1];

            if (static::isClass($key)) {

                $this->di[$key] = $this->di[$key] ?? [
                    'config' => $this->config($key),
                    'files' => \is_string($value) ? [$value] : [],
                    'containers' => [],
                    'arrays' => [],
                ];

                if (isset($this->di[$parent])) {
                    $this->di[$parent]['containers'][] = $key;
                }

            } else if (isset($this->di[$parent])) {
                if (\is_numeric($key)) {
                    $this->di[$parent][self::isClass($value) ? 'containers' : 'files'][] = $value;
                } else {
                    $this->di[$parent]['arrays'][$key] = $value;
                }
            }
        }
    }

    /**
     * @param string $container
     *
     * @return array
     */
    private function config(string $container): array
    {
        return ($this->containerConfig[$container] ?? []) + $this->defaultConfig;
    }

    /**
     * @param string $candidate
     *
     * @return bool
     */
    private static function isClass(string $candidate): bool
    {
        return strpos($candidate, '@') === 0;
    }
}
