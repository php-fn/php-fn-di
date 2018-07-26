#!/usr/bin/env php
<?php

echo call_user_func(require 'vendor/autoload.php', function(Psr\Container\ContainerInterface $container) {
    return get_class($container);
});
