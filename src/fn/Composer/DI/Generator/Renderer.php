<?php
/**
 * (c) php-fn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace fn\Composer\DI\Generator;

/**
 */
class Renderer
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $containers;

    /**
     * @var array
     */
    private $files;

    /**
     * @var array
     */
    private $values;

    /**
     * @param string $class
     * @param array $config
     * @param array $containers
     * @param array $files
     * @param array $values
     */
    public function __construct(
        string $class,
        array $config = [],
        array $containers = [],
        array $files = [],
        array $values = []
    ) {
        $this->class = $class;
        $this->config = $config;
        $this->containers = $containers;
        $this->files = $files;
        $this->values = $values;
    }

    public function getNameSpace(): string
    {
        return \substr($this->class, 0, -(\strlen($this->getClassName()) + 1));
    }

    public function getClassName(): string
    {
        $parts = \explode('\\', $this->class);
        return (string) end($parts);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return 'todo';
    }
}
