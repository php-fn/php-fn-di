<?php
/**
 * (c) php-fn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace fn\DI {
    const WIRING = 'wiring';
}

namespace fn\DI\WIRING {
    const NONE       = null;
    const REFLECTION = 'reflection';
    const STRICT     = 'strict';
    const TOLERANT   = 'tolerant';
}


namespace fn {
    /**
     * @param string|array $config
     * @return DI\ContainerConfigurationFactory
     */
    function di($config = DI\WIRING\NONE): DI\ContainerConfigurationFactory
    {
        $config = is_array($config) ? $config : [DI\WIRING => $config];
        return new DI\ContainerConfigurationFactory($config);
    }
}
