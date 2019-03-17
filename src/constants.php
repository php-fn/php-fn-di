<?php
/**
 * Copyright (C) php-fn. See LICENSE file for license details.
 */

namespace fn\PHP {
    const RESERVED = [
        '__halt_compiler',
        'abstract',
        'and',
        'array',
        'as',
        'break',
        'callable',
        'case',
        'catch',
        'class',
        'clone',
        'const',
        'continue',
        'declare',
        'default',
        'die',
        'do',
        'echo',
        'else',
        'elseif',
        'empty',
        'enddeclare',
        'endfor',
        'endforeach',
        'endif',
        'endswitch',
        'endwhile',
        'eval',
        'exit',
        'extends',
        'final',
        'for',
        'foreach',
        'function',
        'global',
        'goto',
        'if',
        'implements',
        'include',
        'include_once',
        'instanceof',
        'insteadof',
        'interface',
        'isset',
        'list',
        'namespace',
        'new',
        'or',
        'print',
        'private',
        'protected',
        'public',
        'require',
        'require_once',
        'return',
        'static',
        'switch',
        'throw',
        'trait',
        'try',
        'unset',
        'use',
        'var',
        'while',
        'xor'
    ];
}

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
