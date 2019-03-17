<?php
/**
 * Copyright (C) php-fn. See LICENSE file for license details.
 */

namespace fn\DI {
    const WIRING  = 'wiring';
    const CACHE   = 'cache';
    const PROXY   = 'proxy';
    const COMPILE = 'compile';
}

namespace fn\DI\WIRING {
    const NONE       = null;
    const AUTO       = true;
    const REFLECTION = 'reflection';
    const STRICT     = 'strict';
    const TOLERANT   = 'tolerant';
}
