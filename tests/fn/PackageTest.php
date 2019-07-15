<?php
/**
 * Copyright (C) php-fn. See LICENSE file for license details.
 */

namespace fn;

use fn\test\assert;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @coversDefaultClass Package
 */
class PackageTest extends TestCase
{
    private const PACKAGES = [
        'foo' => [
            'name'        => 'foo',
            'version'     => '1.2.3.0',
            'description' => 'foo-d',
            'homepage'    => 'foo-h',
            'authors'     => ['foo-a'],
            'dir'         => '/foo-dir/',
            'root'        => true,
            'extra'       => ['foo-e'],
        ]
    ];

    /**
     * @inheritDoc
     */
    public function setUp()
    {
        $prop = (new ReflectionClass(Package::class))->getProperty('packages');
        $prop->setAccessible(true);
        $prop->setValue(null);
    }

    /**
     * @covers \fn\Package::get
     * @covers \fn\Package::resolveName
     * @covers \fn\Package::resolveVersion
     * @covers \fn\Package::resolveHomepage
     * @covers \fn\Package::resolveDescription
     * @covers \fn\Package::resolveDir
     * @covers \fn\Package::resolveAuthors
     * @covers \fn\Package::resolveExtra
     * @covers \fn\Package::resolveRoot
     * @covers \fn\Package::file
     * @covers \fn\Package::files
     * @covers \fn\Package::version
     */
    public function testNullObject(): void
    {
        assert\type(Package::class, Package::get('foo'));
        assert\same(Package::get('foo'), $package = package('bar'));
        assert\same(null, package('bar', true));
        assert\same(null, $package->name);
        assert\same(null, $package->version);
        assert\same(null, $package->homepage);
        assert\same(null, $package->description);
        assert\same(null, $package->dir);
        assert\false($package->root);
        assert\same([], $package->extra);
        assert\same([], $package->authors);
        assert\same('foo/bar', $package->file('foo/bar'));
        assert\same('/foo/bar', $package->file('/foo/bar'));
        assert\same(['foo/bar', '/foo/bar'], $package->files('foo/bar', '/foo/bar'));
        assert\same('', $package->version());
        assert\same('', $package->version(1));
        assert\same('', $package->version(2));
        assert\same('', $package->version(3));
        assert\same(null, $package->version(true));
    }

    /**
     * @covers \fn\Package::get
     * @covers \fn\Package::resolveName
     * @covers \fn\Package::resolveVersion
     * @covers \fn\Package::resolveHomepage
     * @covers \fn\Package::resolveDescription
     * @covers \fn\Package::resolveDir
     * @covers \fn\Package::resolveAuthors
     * @covers \fn\Package::resolveExtra
     * @covers \fn\Package::resolveRoot
     * @covers \fn\Package::file
     * @covers \fn\Package::files
     * @covers \fn\Package::version
     */
    public function testDefined(): void
    {
        defined('fn\\PACKAGES') || define('fn\\PACKAGES', self::PACKAGES);

        $package = package('foo');
        assert\same('foo', $package->name);
        assert\same('1.2.3.0', $package->version);
        assert\same('foo-h', $package->homepage);
        assert\same('foo-d', $package->description);
        assert\same('/foo-dir/', $package->dir);
        assert\true($package->root);
        assert\same(['foo-e'], $package->extra);
        assert\same(['foo-a'], $package->authors);
        assert\same('/foo-dir/foo/bar', $package->file('foo/bar'));
        assert\same('/foo/bar', $package->file('/foo/bar'));
        assert\same(['/foo-dir/foo/bar', '/foo/bar'], $package->files('foo/bar', '/foo/bar'));
        assert\same('1.2.3', $package->version());
        assert\same('1', $package->version(1));
        assert\same('1.2', $package->version(2));
        assert\same('1.2.3', $package->version(3));
        assert\same('1.2.3.0', $package->version(true));
    }

    /**
     * @covers \fn\Package::version
     */
    public function testVersion(): void
    {
        assert\same('1.2.3.4', (new Package(['version' => '1.2.3.4']))->version());
        assert\same('1.2.3', (new Package(['version' => '1.2.3.0']))->version());
    }
}
