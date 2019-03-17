<?php
/**
 * Copyright (C) php-fn. See LICENSE file for license details.
 */

namespace fn\Composer;

use Composer\Package\RootPackageInterface;
use fn;
use Composer\Composer;
use Composer\Package\CompletePackageInterface;
use Composer\Package\PackageInterface;
use IteratorAggregate;
use Symfony\Component\Filesystem\Filesystem as FS;
use Composer\Util\Filesystem;

/**
 * generate namespace constants for installed packages on autoload dump
 */
class DIPackages implements IteratorAggregate
{
    public $vendorDir;
    private $composer;

    /**
     * @param Composer $composer
     */
    public function __construct(Composer $composer)
    {
        $this->composer  = $composer;
        $this->vendorDir = (new Filesystem)->normalizePath(
            realpath(realpath($composer->getConfig()->get('vendor-dir')))
        ) . '/';
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        $packages   = $this->composer->getRepositoryManager()->getLocalRepository()->getPackages();
        $packages[] = $this->composer->getPackage();
        $im = $this->composer->getInstallationManager();
        $fs = new FS;

        return fn\map($packages, function(PackageInterface $package) use($im, $fs) {
            if (strpos($name = $package->getName(), '/') === false) {
                return null;
            }
            $path = $package instanceof RootPackageInterface ? null : $im->getInstallPath($package);
            $dir  = $path ? $fs->makePathRelative($path, $this->vendorDir) : null;
            [$vendor, $library] = explode('/', $name);
            return fn\mapGroup($vendor)->andKey($library)->andValue([
                'name'        => $name,
                'version'     => $package->getVersion(),
                'description' => $package instanceof CompletePackageInterface ? $package->getDescription() : null,
                'homepage'    => $package instanceof CompletePackageInterface ? $package->getHomepage() : null,
                'authors'     => $package instanceof CompletePackageInterface ? $package->getAuthors() : [],
                'dir'         => $dir,
                'extra'       => $package->getExtra(),
            ]);
        });
    }

    private function up($string): string
    {
        $string .= fn\hasValue(strtolower($string), fn\PHP\RESERVED) ?  '_' : '';
        return str_replace('-', '_', strtoupper($string));
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        $ns    = [];
        $const = [
            '',
            'namespace fn {',
            '    const PACKAGES = [',
        ];

        foreach ($this as $vendor => $packages) {
            $ns[] = "namespace fn\\VENDOR\\{$this->up($vendor)} {";
            foreach ($packages as $name => $package) {
                $ns[] = "    const {$this->up($name)} = '{$package['name']}';";

                $dir = $package['dir'] ? "VENDOR_DIR . '{$package['dir']}'" : 'BASE_DIR';

                $const[] = "        VENDOR\\{$this->up($vendor)}\\{$this->up($name)} => [";
                $const[] = "            'name'        => '{$package['name']}',";
                $const[] = "            'version'     => '{$package['version']}',";
                $const[] = "            'description' => " . var_export($package['description'], true) . ',';
                $const[] = "            'homepage'    => " . var_export($package['homepage'], true) . ',';
                $const[] = "            'dir'         => $dir,";
                $const[] = "            'authors'     => " . new fn\ArrayExport((array)$package['authors']) . ',';
                $const[] = "            'extra'       => " . new fn\ArrayExport($package['extra']) . ',';
                $const[] = '        ],';
                $const[] = '';
            }
            $ns[] = '}';
            $ns[] = '';
        }
        $const[] = '    ];';
        $const[] = '}';

        return implode(PHP_EOL, $ns) . implode(PHP_EOL, $const);
    }
}
