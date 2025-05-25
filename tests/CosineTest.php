<?php

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use Edgaras\StrSim\Cosine;

class CosineTest extends TestCase
{
    public function testIdenticalStrings()
    {
        $this->assertEqualsWithDelta(1.0, Cosine::similarity("abc", "abc"), 1e-10);
    }

    public function testCompletelyDifferentStrings()
    {
        $this->assertEquals(0.0, Cosine::similarity("abc", "xyz"));
    }

    public function testPartiallySimilarStrings()
    {
        $a = "night";
        $b = "nacht";
        $result = Cosine::similarity($a, $b);
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testEmptyStrings()
    {
        $this->assertEquals(0.0, Cosine::similarity("", ""));
    }

    public function testOneEmptyString()
    {
        $this->assertEquals(0.0, Cosine::similarity("abc", ""));
        $this->assertEquals(0.0, Cosine::similarity("", "abc"));
    }
    

    public function testIdenticalVectors()
    {
        $this->assertEqualsWithDelta(1.0, Cosine::similarityFromVectors([1, 2, 3], [1, 2, 3]), 1e-10);
    }

    public function testCompletelyOppositeVectors()
    {
        $this->assertEqualsWithDelta(-1.0, Cosine::similarityFromVectors([1, 0], [-1, 0]), 1e-10);
    }

    public function testOrthogonalVectors()
    {
        $this->assertEqualsWithDelta(0.0, Cosine::similarityFromVectors([1, 0], [0, 1]), 1e-10);
    }

    public function testPartiallyAlignedVectors()
    {
        $a = [1, 1];
        $b = [1, 0];
        $similarity = Cosine::similarityFromVectors($a, $b);
        $this->assertGreaterThan(0, $similarity);
        $this->assertLessThan(1, $similarity);
    }

    public function testMismatchedVectorLengths()
    {
        $this->expectException(\InvalidArgumentException::class);
        Cosine::similarityFromVectors([1, 2, 3], [1, 2]);
    }

    public function testZeroVectors()
    {
        $this->assertEquals(0.0, Cosine::similarityFromVectors([0, 0], [0, 0]));
    }
}
