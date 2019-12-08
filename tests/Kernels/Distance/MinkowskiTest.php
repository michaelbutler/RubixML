<?php

namespace Rubix\ML\Tests\Kernels\Distance;

use Rubix\ML\Kernels\Distance\Minkowski;
use Rubix\ML\Kernels\Distance\Distance;
use PHPUnit\Framework\TestCase;
use Generator;

class MinkowskiTest extends TestCase
{
    /**
     * @var \Rubix\ML\Kernels\Distance\Minkowski
     */
    protected $kernel;

    public function setUp() : void
    {
        $this->kernel = new Minkowski(3.0);
    }

    public function test_build_distance_kernel() : void
    {
        $this->assertInstanceOf(Minkowski::class, $this->kernel);
        $this->assertInstanceOf(Distance::class, $this->kernel);
    }

    /**
     * @dataProvider compute_provider
     */
    public function test_compute(array $a, array $b, float $expected) : void
    {
        $distance = $this->kernel->compute($a, $b);

        $this->assertGreaterThanOrEqual(0., $distance);
        $this->assertEquals($expected, $distance);
    }

    public function compute_provider() : Generator
    {
        yield [[2, 1, 4, 0], [-2, 1, 8, -2],  5.14256318131647];

        yield [[7.4, -2.5], [0.01, -1], 7.410542673140729];
        
        yield [[1000, -2000, 3000], [1000, -2000, 3000], 0.0];
    }
}
