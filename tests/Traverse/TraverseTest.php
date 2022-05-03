<?php

/*
 * This file is part of the traverse/reshape package.
 *
 * (c) Alley <info@alley.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alley\Tests\Traverse;

use Alley\Tests\Traverse\Fixtures\ArrayAccessible;
use PHPUnit\Framework\TestCase;

use function Alley\traverse;

final class TraverseTest extends TestCase
{
    /**
     * Test `traverse()` against an array.
     */
    public function testTraverseArray()
    {
        $arr = [
            'alpha' => [
                'bravo' => [
                    'charlie' => 'delta',
                ],
            ],
        ];

        $this->assertSame(['charlie' => 'delta'], traverse($arr, 'alpha.bravo'));
        $this->assertSame('delta', traverse($arr, 'alpha.bravo.charlie'));
        $this->assertSame('delta', traverse($arr, 'alpha/bravo/charlie', '/'));
        $this->assertNull(traverse($arr, 'alpha.bravo.charlie.delta'));
        $this->assertNull(traverse($arr, 'zebra'));
    }

    /**
     * Test `traverse` against an object.
     */
    public function testTraverseObject()
    {
        $charlie = (object)[
            'charlie' => 'delta',
        ];

        $object = (object)[
            'alpha' => (object)[
                'bravo' => $charlie,
            ],
        ];

        $this->assertSame($charlie, traverse($object, 'alpha.bravo'));
        $this->assertSame('delta', traverse($object, 'alpha.bravo.charlie'));
        $this->assertSame('delta', traverse($object, 'alpha/bravo/charlie', '/'));
        $this->assertNull(traverse($object, 'alpha.bravo.charlie.delta'));
        $this->assertNull(traverse($object, 'zebra'));
    }

    /**
     * Test `traverse` against an ArrayAccess instance.
     */
    public function testTraverseArrayAccess()
    {
        $charlie = new ArrayAccessible();
        $charlie['charlie'] = 'delta';

        $bravo = new ArrayAccessible();
        $bravo['bravo'] = $charlie;

        $object = new ArrayAccessible();
        $object['alpha'] = $bravo;

        $this->assertSame($charlie, traverse($object, 'alpha.bravo'));
        $this->assertSame('delta', traverse($object, 'alpha.bravo.charlie'));
        $this->assertSame('delta', traverse($object, 'alpha/bravo/charlie', '/'));
        $this->assertNull(traverse($object, 'alpha.bravo.charlie.delta'));
        $this->assertNull(traverse($object, 'zebra'));
    }

    /**
     * Test `traverse()` against a value with mixed types.
     */
    public function testTraverseMixedTypes()
    {
        $source = [
            'alpha' => (object)[
                'bravo' => [
                    'charlie' => 'delta',
                ],
            ],
        ];

        $this->assertSame(['charlie' => 'delta'], traverse($source, 'alpha.bravo'));
        $this->assertSame('delta', traverse($source, 'alpha.bravo.charlie'));
        $this->assertSame('delta', traverse($source, 'alpha/bravo/charlie', '/'));
        $this->assertNull(traverse($source, 'alpha.bravo.charlie.delta'));
        $this->assertNull(traverse($source, 'zebra'));
    }

    /**
     * Test the ability to fetch multiple values with `traverse()`.
     */
    public function testTraverseMultiplePaths()
    {
        $arr = [
            'alpha' => 'bravo',
            'charlie' => 'delta',
            'echo' => [
                'foxtrot' => 'golf',
                'hotel' => 'india',
            ],
        ];

        $this->assertSame(
            ['bravo', 'delta'],
            traverse($arr, ['alpha', 'charlie'])
        );

        $this->assertSame(
            ['delta', ['golf', 'india']],
            traverse(
                $arr,
                [
                    'charlie',
                    [
                        'echo.foxtrot',
                        'echo.hotel',
                    ],
                ]
            )
        );
    }

    /**
     * Test that passing a string array key to `traverse()` will cause the data
     * source to be traversed to the location in key before fetching values.
     */
    public function testTraversePathInKey()
    {
        $arr = [
            'alpha' => 'bravo',
            'charlie' => 'delta',
            'echo' => [
                'foxtrot' => 'golf',
                'hotel' => 'india',
            ],
            'juliette' => [
                'kilo' => [
                    'lima' => [
                        'mike' => 'november',
                        'oscar' => 'papa',
                    ],
                ],
            ],
        ];


        $this->assertSame(
            ['delta', ['golf', 'india']],
            traverse(
                $arr,
                [
                    'charlie',
                    'echo' => ['foxtrot', 'hotel'],
                ]
            )
        );

        $this->assertSame(
            [['november', 'papa']],
            traverse(
                $arr,
                [
                    'juliette.kilo.lima' => ['mike', 'oscar'],
                ]
            )
        );
    }

    /**
     * Test that `traverse()` handles a malformed path.
     */
    public function testTraverseInvalidPath()
    {
        $this->assertNull(traverse([], true));
    }
}
