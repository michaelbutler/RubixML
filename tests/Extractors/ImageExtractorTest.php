<?php

use Rubix\Engine\Extractors\Extractor;
use Rubix\Engine\Extractors\ImageScanner;
use PHPUnit\Framework\TestCase;

class ImageScannerTest extends TestCase
{
    protected $extractor;

    protected $samples;

    public function setUp()
    {
        $this->samples = [
            imagecreatefromjpeg(__DIR__ . '/space.jpg'),
        ];

        $this->extractor = new ImageScanner([3, 3], true);
    }

    public function test_build_count_vectorizer()
    {
        $this->assertInstanceOf(ImageScanner::class, $this->extractor);
        $this->assertInstanceOf(Extractor::class, $this->extractor);
    }

    public function test_transform_dataset()
    {
        $this->extractor->fit($this->samples);

        $samples = $this->extractor->extract($this->samples);

        $this->assertEquals([
            [22, 35, 60, 53, 66, 102, 29, 44, 73, 36, 49, 79, 45, 57, 89, 21,
            32, 56, 44, 53, 85, 43, 49, 75, 12, 18, 34],
        ], $samples);
    }
}
