<?php

namespace Rubix\ML\Tests\Transformers;

use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\Transformers\Transformer;
use Rubix\ML\Transformers\HTMLStripper;
use PHPUnit\Framework\TestCase;

class HTMLStripperTest extends TestCase
{
    /**
     * @var \Rubix\ML\Datasets\Unlabeled
     */
    protected $dataset;

    /**
     * @var \Rubix\ML\Transformers\HTMLStripper
     */
    protected $transformer;

    public function setUp() : void
    {
        $this->dataset = Unlabeled::quick([
            ['The quick brown fox <br />jumped over the <b>lazy</b> man sitting at a bus'
                . ' stop drinking a can of <a href="http://coke.com">Coke</a>'],
            ['with a <i>Dandy</i> ubrella'],
        ]);

        $this->transformer = new HTMLStripper();
    }

    public function test_build_transformer() : void
    {
        $this->assertInstanceOf(HTMLStripper::class, $this->transformer);
        $this->assertInstanceOf(Transformer::class, $this->transformer);
    }

    public function test_transform() : void
    {
        $this->dataset->apply($this->transformer);
    
        $outcome = [
            ['The quick brown fox jumped over the lazy man sitting at a bus'
                . ' stop drinking a can of Coke'],
            ['with a Dandy ubrella'],
        ];
    
        $this->assertEquals($outcome, $this->dataset->samples());
    }
}
