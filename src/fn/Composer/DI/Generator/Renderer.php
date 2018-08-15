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
     * @var ArrayExport
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
     * @var ArrayExport
     */
    private $values;

    /**
     * @var bool
     */
    private $root;

    /**
     * @param string $class
     * @param array $config
     * @param array $containers
     * @param array $files
     * @param array $values
     * @param bool $root
     */
    public function __construct(
        string $class,
        array $config = [],
        array $containers = [],
        array $files = [],
        array $values = [],
        bool $root = false
    ) {
        $this->class = $class;
        $this->config = new ArrayExport($config);

        $this->containers = implode('', \array_map(function(string $container): string {
            return "\n                    \\{$container}::class => new \\{$container}(\$this),";
        }, $containers));

        $this->files = implode('', \array_map(function(string $file): string {
            return "\n                \$rootDir . '$file',";
        }, $files));

        $this->values = new ArrayExport($values);
        $this->root = $root;
    }

    public function getNameSpace(): string
    {
        return \substr($this->class, 0, -(\strlen($this->getClassName()) + 1));
    }

    public function getClassName(): string
    {
        $parts = \explode('\\', $this->class);

        return (string)end($parts);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $wrapper = ['\Psr\Container\ContainerInterface $wrapper', '$wrapper'];
        if ($this->root) {
            $wrapper = ['', 'null'];
        }

        return <<<EOF
namespace {$this->getNameSpace()} {
    /**
     */
    class {$this->getClassName()} extends \DI\Container
    {
        /**
         * @inheritdoc
         */
        public function __construct({$wrapper[0]})
        {
            \$rootDir = \\dirname(__DIR__, 7) . DIRECTORY_SEPARATOR;

            \$cc = ContainerConfigurationFactory::create(
                {$this->config}, 
                {$wrapper[1]},
                [{$this->containers}
                ],{$this->files}
                {$this->values}
            );

            parent::__construct(
                \$cc->getDefinitionSource(),
                \$cc->getProxyFactory(),
                \$cc->getWrapperContainer()
            );        
        }
    }
}
EOF;
    }
}
