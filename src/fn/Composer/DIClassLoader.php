<?php
/**
 * (c) php-fn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace fn\Composer;

use Composer\Autoload;

/**
 */
class DIClassLoader extends Autoload\ClassLoader
{
    /**
     * @var Autoload\ClassLoader
     */
    private $classLoader;

    /**
     * @param Autoload\ClassLoader|null $classLoader
     *
     * @return static
     */
    public static function instance(Autoload\ClassLoader $classLoader = null): self
    {
        static $instance;
        if (!$instance) {
            $instance = new self($classLoader);
        }
        return $instance;
    }

    /**
     * @param Autoload\ClassLoader $proxy
     */
    private function __construct(Autoload\ClassLoader $proxy)
    {
        $this->classLoader = $proxy;
    }

    /**
     * @param callable $callable
     * @param array    $params
     *
     * @return mixed
     */
    public function __invoke($callable, array $params = [])
    {
        static $di;
        /** @var \fn\DI\Container $di */
        $di = $di ?: new DI;
        return $di->call($callable, $params);
    }

    /**
     * @inheritdoc
     */
    public function getPrefixes()
    {
        return \call_user_func_array([$this->classLoader, __FUNCTION__], \func_get_args());
    }

    /**
     * @inheritdoc
     */
    public function getPrefixesPsr4()
    {
        return \call_user_func_array([$this->classLoader, __FUNCTION__], \func_get_args());
    }

    /**
     * @inheritdoc
     */
    public function getFallbackDirs()
    {
        return \call_user_func_array([$this->classLoader, __FUNCTION__], \func_get_args());
    }

    /**
     * @inheritdoc
     */
    public function getFallbackDirsPsr4()
    {
        return \call_user_func_array([$this->classLoader, __FUNCTION__], \func_get_args());
    }

    /**
     * @inheritdoc
     */
    public function getClassMap()
    {
        return \call_user_func_array([$this->classLoader, __FUNCTION__], \func_get_args());
    }

    /**
     * @inheritdoc
     */
    public function addClassMap(array $classMap)
    {
        return \call_user_func_array([$this->classLoader, __FUNCTION__], \func_get_args());
    }

    /**
     * @inheritdoc
     */
    public function add($prefix, $paths, $prepend = false)
    {
        return \call_user_func_array([$this->classLoader, __FUNCTION__], \func_get_args());
    }

    /**
     * @inheritdoc
     */
    public function addPsr4($prefix, $paths, $prepend = false)
    {
        return \call_user_func_array([$this->classLoader, __FUNCTION__], \func_get_args());
    }

    /**
     * @inheritdoc
     */
    public function set($prefix, $paths)
    {
        return \call_user_func_array([$this->classLoader, __FUNCTION__], \func_get_args());
    }

    /**
     * @inheritdoc
     */
    public function setPsr4($prefix, $paths)
    {
        return \call_user_func_array([$this->classLoader, __FUNCTION__], \func_get_args());
    }

    /**
     * @inheritdoc
     */
    public function setUseIncludePath($useIncludePath)
    {
        return \call_user_func_array([$this->classLoader, __FUNCTION__], \func_get_args());
    }

    /**
     * @inheritdoc
     */
    public function getUseIncludePath()
    {
        return \call_user_func_array([$this->classLoader, __FUNCTION__], \func_get_args());
    }

    /**
     * @inheritdoc
     */
    public function setClassMapAuthoritative($classMapAuthoritative)
    {
        return \call_user_func_array([$this->classLoader, __FUNCTION__], \func_get_args());
    }

    /**
     * @inheritdoc
     */
    public function isClassMapAuthoritative()
    {
        return \call_user_func_array([$this->classLoader, __FUNCTION__], \func_get_args());
    }

    /**
     * @inheritdoc
     */
    public function setApcuPrefix($apcuPrefix)
    {
        return \call_user_func_array([$this->classLoader, __FUNCTION__], \func_get_args());
    }

    /**
     * @inheritdoc
     */
    public function getApcuPrefix()
    {
        return \call_user_func_array([$this->classLoader, __FUNCTION__], \func_get_args());
    }

    /**
     * @inheritdoc
     */
    public function register($prepend = false)
    {
        return \call_user_func_array([$this->classLoader, __FUNCTION__], \func_get_args());
    }

    /**
     * @inheritdoc
     */
    public function unregister()
    {
        return \call_user_func_array([$this->classLoader, __FUNCTION__], \func_get_args());
    }

    /**
     * @inheritdoc
     */
    public function loadClass($class)
    {
        return \call_user_func_array([$this->classLoader, __FUNCTION__], \func_get_args());
    }

    /**
     * @inheritdoc
     */
    public function findFile($class)
    {
        return \call_user_func_array([$this->classLoader, __FUNCTION__], \func_get_args());
    }
}
