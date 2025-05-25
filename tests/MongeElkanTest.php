<?php

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use Edgaras\StrSim\MongeElkan;

class MongeElkanTest extends TestCase
{
    public function testIdenticalSentences()
    {
        $this->assertEqualsWithDelta(1.0, MongeElkan::similarity("john smith", "john smith"), 1e-10);
    }

    public function testPartialMatch()
    {
        $a = "john smith";
        $b = "jon smythe";

        $result = MongeElkan::similarity($a, $b);
        $this->assertGreaterThan(0.0, $result);
        $this->assertLessThan(1.0, $result);
    }

    public function testDifferentWords()
    {
        $this->assertEqualsWithDelta(0.0, MongeElkan::similarity("abc def", "xyz uvw"), 1e-10);
    }

    public function testSingleWordMatch()
    {
        $this->assertEqualsWithDelta(1.0, MongeElkan::similarity("hello", "hello"), 1e-10);
    }

    public function testSingleWordMismatch()
    {
        $similarity = MongeElkan::similarity("hello", "world");
        $this->assertGreaterThan(0.0, $similarity);
        $this->assertLessThan(1.0, $similarity);
    }

    public function testEmptyStrings()
    {
        $this->assertEqualsWithDelta(1.0, MongeElkan::similarity("", ""), 1e-10);
    }

    public function testOneEmpty()
    {
        $this->assertEqualsWithDelta(0.0, MongeElkan::similarity("test", ""), 1e-10);
    }
}
