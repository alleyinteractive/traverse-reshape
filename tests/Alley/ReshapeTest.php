<?php

/*
 * This file is part of the traverse/reshape package.
 *
 * (c) Alley <info@alley.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alley;

use PHPUnit\Framework\TestCase;

final class ReshapeTest extends TestCase
{
    /**
     * `reshape()` should assign the given key in the shape the value at the
     * given path if it exists.
     */
    public function testReshapeDescendIntoArray()
    {
        $original = [
            'foo' => [
                'bar' => 'baz',
            ],
        ];

        $this->assertSame(
            [
                'apple' => 'baz',
                'banana' => null,
            ],
            reshape(
                $original,
                [
                    'apple' => 'foo.bar',
                    'banana' => 'baz.bat',
                ]
            )
        );
    }

    /**
     * `reshape()` should create nested arrays when the given shape has them.
     */
    public function testReshapeIntoMultidimensionalArray()
    {
        $original = [
            'ny' => 'albany',
            'nd' => [
                'cap' => 'bismarck',
            ],
            'sd' => [
                'cap' => 'pierre',
            ],
        ];

        $expected = [
            'new york' => [
                'capital' => 'albany',
            ],
            'dakotas' => [
                'north' => [
                    'capital' => 'bismarck',
                ],
                'south' => [
                    'capital' => 'pierre',
                ],
            ],
        ];

        $this->assertSame(
            $expected,
            reshape(
                $original,
                [
                    'new york' => [
                        'capital' => 'ny',
                    ],
                    'dakotas' => [
                        'north' => [
                            'capital' => 'nd.cap',
                        ],
                        'south' => [
                            'capital' => 'sd.cap',
                        ],
                    ],
                ]
            )
        );
    }

    /**
     * `reshape()` should return an object when the shape is an object.
     */
    public function testReshapeIntoObject()
    {
        $original = [
            'foo' => [
                'bar' => 'baz',
            ],
        ];

        $actual = reshape(
            $original,
            (object) [
                'apple' => 'foo.bar',
                'banana' => 'baz.bat',
            ]
        );

        $this->assertIsObject($actual);
        $this->assertSame('baz', $actual->apple);
        $this->assertSame(null, $actual->banana);
    }

    /**
     * `reshape()` should infer a key from an indexed path to a value within the shape.
     */
    public function testReshapeWithShorthandPaths()
    {
        $original = [
            'title' => 'hello, world!',
            'specs' => [
                'language' => 'php',
                'version' => '9.9',
            ],
        ];

        $expected = [
            'title' => 'hello, world!',
            'language' => 'php',
            'ver' => '9.9',
        ];

        $this->assertSame(
            $expected,
            reshape(
                $original,
                [
                    'title',
                    'specs.language',
                    'ver' => 'specs.version',
                ]
            )
        );
    }

    /**
     * Test that multiple sources can be combined into a single source and then reshaped.
     */
    public function testReshapeMultipleSources()
    {
        $a = [
            'carrier' => 'delta',
            'aircraft' => 'A380',
        ];

        $b = (object)[
            'carrier' => 'southwest',
            'aircraft' => '737',
        ];

        $this->assertSame(
            [
                'carrier' => 'southwest',
                'aircraft' => 'A380',
            ],
            reshape(
                [$a, $b],
                [
                    '1.carrier',
                    '0.aircraft',
                ]
            )
        );
    }

    /**
     * Test that `reshape()` uses the last of a duplicate key in the given shape.
     */
    public function testReshapeKeyCollision()
    {
        $original = [
            'united states' => [
                'capital' => 'washington, d.c.',
                'currency' => 'usd',
                'states' => [
                    'alaska' => [
                        'capital' => 'juneau',
                    ],
                ],
            ],
        ];

        $this->assertSame(
            [
                'currency' => 'usd',
                'capital' => 'juneau',
            ],
            reshape(
                $original,
                [
                    'capital' => ['united states' => 'capital'],
                    'united states.currency',
                    'united states.states.alaska.capital',
                ]
            )
        );
    }
}
