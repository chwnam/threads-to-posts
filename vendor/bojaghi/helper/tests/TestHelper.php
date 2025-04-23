<?php

namespace Bojaghi\Helper\Tests;

use Bojaghi\Helper\Helper;

class TestHelper extends \WP_UnitTestCase
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
                    ['sampleKey' => 'sampleValue'], // Associative part
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
                    [100, 200, 300]
                ],
                // Input
                [
                    'x' => 100,
                    'y' => 200,
                    'z' => 300,
                    0   => 100,
                    1   => 200,
                    2   => 300,
                ]
            ],
        ];
    }
}
