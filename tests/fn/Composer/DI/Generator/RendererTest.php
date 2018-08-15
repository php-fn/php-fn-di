<?php
/**
 * (c) php-fn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace fn\Composer\DI\Generator;

use fn\test\assert;

/**
 * @coversDefaultClass Renderer
 */
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

    /**
     * @return array
     */
    public function providerToString(): array
    {
        return [
            'root' => [
                <<<EOF
namespace ns1\\ns2 {
    /**
     */
    class c1 extends \\DI\\Container
    {
        /**
         * @inheritdoc
         */
        public function __construct()
        {
            \$rootDir = \dirname(__DIR__, 7) . DIRECTORY_SEPARATOR;

            \$cc = ContainerConfigurationFactory::create(
                ['wiring' => 'reflection', 'cache' => false, 'proxy' => 'proxy.php', 'compile' => '/tmp/'], 
                null,
                [
                    \\ns1\\ns2\\ns3\\c2::class => new \\ns1\\ns2\\ns3\\c2(\$this),
                    \\ns1\\ns2\\c3::class => new \\ns1\\ns2\\c3(\$this),
                    \\ns1\\c3::class => new \\ns1\\c3(\$this),
                    \\c4::class => new \\c4(\$this),
                ],
                \$rootDir . 'config/c1.php',
                \$rootDir . 'config/c2.php',
                ['k1' => 'v1', 'k2' => ['v2', 'v3'], 'k3' => ['k4' => ['v5']]]
            );

            parent::__construct(
                \$cc->getDefinitionSource(),
                \$cc->getProxyFactory(),
                \$cc->getWrapperContainer()
            );        
        }
    }
}
EOF
, new Renderer('ns1\ns2\c1', [
        'wiring' => 'reflection',
        'cache' => false,
        'proxy' => 'proxy.php',
        'compile' => '/tmp/',
    ], [
        'ns1\\ns2\\ns3\\c2',
        'ns1\\ns2\\c3',
        'ns1\\c3',
        'c4',
    ], [
        'config/c1.php',
        'config/c2.php',
    ], [
        'k1' => 'v1',
        'k2' => ['v2', 'v3'],
        'k3' => ['k4' => ['v5']],
    ], true),
],

            'empty' => [
                <<<EOF
namespace  {
    /**
     */
    class c1 extends \\DI\\Container
    {
        /**
         * @inheritdoc
         */
        public function __construct(\\Psr\\Container\\ContainerInterface \$wrapper)
        {
            \$rootDir = \dirname(__DIR__, 7) . DIRECTORY_SEPARATOR;

            \$cc = ContainerConfigurationFactory::create(
                [], 
                \$wrapper,
                [
                ],
                []
            );

            parent::__construct(
                \$cc->getDefinitionSource(),
                \$cc->getProxyFactory(),
                \$cc->getWrapperContainer()
            );        
        }
    }
}
EOF
, new Renderer('c1')]
        ];
    }

    /**
     * @covers Renderer::__toString
     *
     * @dataProvider providerToString
     *
     * @param string $expected
     * @param Renderer $renderer
     */
    public function testToString(string $expected, Renderer $renderer)
    {
        assert\same($expected, (string)$renderer);
    }
}
