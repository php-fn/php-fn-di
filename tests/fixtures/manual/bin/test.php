#!/usr/bin/env php
<?php

echo call_user_func(require '/tmp/vendor-di-test-manual/autoload.php', function(Psr\Container\ContainerInterface $container) {
    return $container->get('foo') . $container->get('bar');
});
