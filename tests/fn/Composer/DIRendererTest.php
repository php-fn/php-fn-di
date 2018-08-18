<?php
/**
 * (c) php-fn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace fn\Composer;

use fn\Composer\DIRenderer;
use fn\test\assert;

/**
 * @coversDefaultClass DIRenderer
 */
class DIRendererTest extends \PHPUnit_Framework_TestCase
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
     * @covers       DIRenderer::getNameSpace
     * @covers       DIRenderer::getClassName
     *
     * @dataProvider providerClass
     *
     * @param string $expectedClassName
     * @param string $expectedNameSpace
     * @param string $class
     */
    public function testClass(string $expectedClassName, string $expectedNameSpace, string $class)
    {
        $renderer = new DIRenderer($class);
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
            \$cc = \\fn\\DI\\ContainerConfigurationFactory::create(
                ['wiring' => 'reflection', 'cache' => false, 'proxy' => 'proxy.php', 'compile' => '/tmp/'], 
                null,
                [
                    \\ns1\\ns2\\ns3\\c2::class => new \\ns1\\ns2\\ns3\\c2(\$this),
                    \\ns1\\ns2\\c3::class => new \\ns1\\ns2\\c3(\$this),
                    \\ns1\\c3::class => new \\ns1\\c3(\$this),
                    \\c4::class => new \\c4(\$this),
                ],
                \\fn\\BASE_DIR . 'config/c1.php',
                \\fn\\BASE_DIR . 'config/c2.php',
                ['k1' => 'v1', 'k2' => ['v2', 'v3'], 'k3' => ['k4' => ['v5']]]
            );

            parent::__construct(\$cc->getDefinitionSource(), \$cc->getProxyFactory(), \$cc->getWrapperContainer());        
        }
    }
}
EOF
, new DIRenderer('ns1\ns2\c1', [
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
            \$cc = \\fn\\DI\\ContainerConfigurationFactory::create(
                [], 
                \$wrapper,
                [
                ],
                []
            );

            parent::__construct(\$cc->getDefinitionSource(), \$cc->getProxyFactory(), \$cc->getWrapperContainer());        
        }
    }
}
EOF
, new DIRenderer('c1')]
        ];
    }

    /**
     * @covers DIRenderer::__toString
     *
     * @dataProvider providerToString
     *
     * @param string $expected
     * @param DIRenderer $renderer
     */
    public function testToString(string $expected, DIRenderer $renderer)
    {
        assert\same($expected, (string)$renderer);
    }
}
