<?php

namespace Bojaghi\Helper\Tests;

use Bojaghi\Helper\Helper;
use WP_UnitTestCase;

class TestHelper extends WP_UnitTestCase
{
    public function test_loadConfig(): void
    {
        $path   = __DIR__ . '/test-config.php';
        $sample = ['key1' => 'value1', 'key2' => 'value2'];

        $this->assertEquals($sample, Helper::loadConfig($sample));
        $this->assertEquals($sample, Helper::loadConfig($path));
    }

    /**
     * Test Helper::separateArray()
     *
     * @dataProvider provider_separateArray())
     */
    public function test_separateArray(array $expected, array $input): void
    {
        $result = Helper::separateArray($input);
        $this->assertEquals($expected, $result);
    }

    protected function provider_separateArray(): array
    {
        return [
            'Test case 1' => [
                // Expected
                [
                    ['a' => 'apple', 'b' => 'banana'], // Associative part
                    [100, 200],                        // Indexed part
                ],
                // Input
                ['a' => 'apple', 'b' => 'banana', 100, 200],
            ],
            'Test case 2' => [
                // Expected
                [
                    ['sampleKey' => 'sampleValue'],         // Associative part
                    ['a', 'b', 'c'],                        // Indexed part
                ],
                // Input
                ['sampleKey' => 'sampleValue', 'a', 'b', 'c'],
            ],
            'Test case 3' => [
                // Expected
                [
                    ['apple' => 420, 'banana' => 360], // Associative part
                    [],                                // Indexed part
                ],
                // Input
                ['apple' => 420, 'banana' => 360],
            ],
            'Test case 4' => [
                // Expected
                [
                    [],              // Associative part
                    ['a', 'b', 'c'], // Indexed part
                ],
                // Input
                ['a', 'b', 'c'],
            ],
            'Test case 5' => [
                // Expected
                [
                    ['x' => 100, 'y' => 200, 'z' => 300],
                    [100, 200, 300],
                ],
                // Input
                [
                    'x' => 100,
                    'y' => 200,
                    'z' => 300,
                    0   => 100,
                    1   => 200,
                    2   => 300,
                ],
            ],
        ];
    }

    public function test_prefix(): void
    {
        $this->assertEquals('prefix_sample', Helper::prefixed('sample', 'prefix_'));

        // Make sure double-prefixing is ignored.
        $this->assertEquals('prefix_sample', Helper::prefixed('prefix_sample', 'prefix_'));
    }

    public function test_unprefixed(): void
    {
        $this->assertEquals('sample', Helper::unprefixed('prefix_sample', 'prefix_'));

        // Make sure un-prefixing is safe against a non-prefixed string.
        $this->assertEquals('sample', Helper::unprefixed('sample', 'prefix_'));;
    }

    /**
     * @param $expected
     * @param $input
     *
     * @return void
     * @dataProvider provider_toCamelCase
     */
    public function test_toCamelCase($expected, $input): void
    {
        $this->assertEquals($expected, Helper::toCamelCase($input));
    }

    protected function provider_toCamelCase(): array
    {
        return [
            'Simple #1' => ['sampleCase', 'sample_case'],
            'Simple #2' => ['sampleCase', 'sampleCase'],
        ];
    }

    /**
     * @param $expected
     * @param $input
     *
     * @return void
     * @dataProvider provider_toSnakeCase
     */
    public function test_toSnakeCase($expected, $input): void
    {
        $this->assertEquals($expected, Helper::toSnakeCase($input));
    }

    protected function provider_toSnakeCase(): array
    {
        return [
            'Simple #1' => ['sample_case', 'sampleCase'],
            'Simple #2' => ['sample_case', 'sample_case'],
        ];
    }
}

