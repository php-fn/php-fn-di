<?php
/**
 * Copyright (C) php-fn. See LICENSE file for license details.
 */

namespace fn;

use fn\test\assert;

/**
 * @covers ArrayExport
 */
class ArrayExportTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return array
     */
    public function providerToString(): array
    {
        return [
            'empty' => ['[]', []],
            'nested empty' => ['[[[]]]', [[[]]]],
            'nested empty count > 1' => ['[[[], []], []]', [[[], []], []]],
            'one element' => ["['a']", ['a']],
            'mixed' => [
                "[['a', 'b'], ['c', ['d', 'e' => 'e']], 'f']",
                [['a', 'b'], ['c', ['d', 'e' => 'e']], 'f']
            ],
            'unordered numeric keys' => [
                "[-5 => -5, 1 => 'a', 'b', 'k' => [2 => 'c'], 'd', 10 => 'e', 5 => 5, 6]",
                [-5 => -5, 1 => 'a', 'b', 'k' => [2 => 'c'], 'd', 10 => 'e', 5 => 5, 6]
            ],
        ];
    }

    /**
     * @covers       ArrayExport::__toString
     *
     * @dataProvider providerToString
     *
     * @param string $expected
     * @param array $array
     */
    public function testToString(string $expected, array $array): void
    {
        $export = new ArrayExport($array);
        assert\same($expected, (string)$export);
    }
}
