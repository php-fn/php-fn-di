<?php
/**
 * (c) php-fn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace fn\DI;

use DI\Definition\Definition;
use DI\Definition\Source\DefinitionSource;

/**
 * @see DefinitionSource
 */
trait DefinitionSourceProxyTrait
{
    /**
     * @var DefinitionSource
     */
    private $definitionSource;

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * @see DefinitionSource::getDefinition
     * @param string $name
     * @return Definition|null
     */
    public function getDefinition(string $name)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->definitionSource->getDefinition($name);
    }

    /**
     * @see DefinitionSource::getDefinitions
     * @return Definition[]
     */
    public function getDefinitions(): array
    {
        return $this->definitionSource->getDefinitions();
    }
}
