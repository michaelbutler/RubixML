<?php

namespace Rubix\ML\Tests\Transformers;

use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\Transformers\Transformer;
use Rubix\ML\Transformers\L1Normalizer;
use PHPUnit\Framework\TestCase;

class L1NormalizerTest extends TestCase
{
    /**
     * @var \Rubix\ML\Datasets\Unlabeled
     */
    protected $dataset;

    /**
     * @var \Rubix\ML\Transformers\L1Normalizer
     */
    protected $transformer;

    public function setUp() : void
    {
        $this->dataset = new Unlabeled([
            [1, 2, 3, 4],
            [40, 20, 30, 10],
            [100, 300, 200, 400],
        ]);

        $this->transformer = new L1Normalizer();
    }

    public function test_build_transformer() : void
    {
        $this->assertInstanceOf(L1Normalizer::class, $this->transformer);
        $this->assertInstanceOf(Transformer::class, $this->transformer);
    }

    public function test_transform() : void
    {
        $this->dataset->apply($this->transformer);

        $this->assertEquals([
            [0.1, 0.2, 0.3, 0.4],
            [0.4, 0.2, 0.3, 0.1],
            [0.1, 0.3, 0.2, 0.4],
        ], $this->dataset->samples());
    }
}
