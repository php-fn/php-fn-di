<?php
/**
 * Copyright (C) php-fn. See LICENSE file for license details.
 */

namespace fn\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;

/**
 */
class DIPlugin implements PluginInterface, EventSubscriberInterface
{
    /**
     * @inheritdoc
     */
    public function activate(Composer $composer, IOInterface $io)
    {
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [ScriptEvents::POST_AUTOLOAD_DUMP => 'onAutoloadDump'];
    }

    /**
     * @param Event $event
     */
    public static function onAutoloadDump(Event $event)
    {
        $composer  = $event->getComposer();
        $packages  = new DIPackages($composer);
        $vendorDir = $packages->vendorDir;
        $extra     = $composer->getPackage()->getExtra();

        $provider  = new DIProvider((array)($extra['di'] ?? []), (array)($extra['di-config'] ?? []));

        self::generateAutoloadFile(
            $file = $vendorDir . 'composer/autoload_php-fn-di.php',
            \implode(PHP_EOL, \iterator_to_array($provider)),
            (string)$packages
        );
        $event->getIO()->write("<info>Autoload class '$file' generated</info>");

        self::modifyAutoloadFile($file = $vendorDir . 'autoload.php');
        $event->getIO()->write("<info>Autoload class '$file' modified</info>");
    }

    /**
     * @param string $file
     */
    private static function modifyAutoloadFile(string $file)
    {
        \file_put_contents($file,  \str_replace(
            [
                '@generated by Composer',
                '::getLoader();',
                'return ComposerAutoloaderInit',
            ],
            [
                '@generated by Composer & @modified by php-fn/di',
                '::getLoader());',
                <<<EOF
return call_user_func(function(\$loader) {
    require_once __DIR__ . '/composer/autoload_php-fn-di.php';
    return fn\\Composer\\DIClassLoader::instance(\$loader);
}, ComposerAutoloaderInit
EOF
            ],
            \file_get_contents($file)
        ));
    }

    private static function generateAutoloadFile(string $file, string $classes, string $packages)
    {

        \file_put_contents($file, <<<EOF
<?php
// @generated by php-fn/di

namespace fn {
    define(__NAMESPACE__ . '\VENDOR_DIR', dirname(dirname(__FILE__)) . \DIRECTORY_SEPARATOR);
    define(__NAMESPACE__ . '\BASE_DIR', dirname(VENDOR_DIR) . \DIRECTORY_SEPARATOR);
}

$packages

$classes
EOF
        );
    }
}
