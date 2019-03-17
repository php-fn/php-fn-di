#!/usr/bin/env php
<?php

(require 'vendor/autoload.php') instanceof fn\Composer\DIClassLoader || fn\fail(__LINE__);

fn\Composer\di() instanceof  fn\Composer\DI || fn\fail(__LINE__);

call_user_func(require 'vendor/autoload.php', function(fn\Composer\DI $composer, fn\DI\Container $di) {
    $composer === $di || fn\fail(__LINE__);
});

echo call_user_func(require 'vendor/autoload.php', function(Psr\Container\ContainerInterface $container) {
    return get_class($container);
});
