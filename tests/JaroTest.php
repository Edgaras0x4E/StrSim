<?php

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use Edgaras\StrSim\Jaro;

class JaroTest extends TestCase
{
    public function testIdenticalStrings()
    {
        $this->assertEqualsWithDelta(1.0, Jaro::distance("martha", "martha"), 1e-10);
    }

    public function testCompletelyDifferentStrings()
    {
        $this->assertEqualsWithDelta(0.0, Jaro::distance("abc", "xyz"), 1e-10);
    }

    public function testPartialMatch()
    {
        $expected = 0.9444444444;
        $actual = Jaro::distance("martha", "marhta");
        $this->assertEqualsWithDelta($expected, $actual, 1e-6);
    }

    public function testCaseWithSomeOverlap()
    {
        $expected = 0.8222222222;
        $actual = Jaro::distance("dwayne", "duane");
        $this->assertEqualsWithDelta($expected, $actual, 1e-6);
    }

    public function testEmptyStrings()
    {
        $this->assertEqualsWithDelta(1.0, Jaro::distance("", ""), 1e-10);
    }

    public function testOneEmptyString()
    {
        $this->assertEqualsWithDelta(0.0, Jaro::distance("abc", ""), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaro::distance("", "abc"), 1e-10);
    }

    public function testSingleCharacterMismatch()
    {
        $this->assertEqualsWithDelta(0.0, Jaro::distance("a", "b"), 1e-10);
    }

    public function testSingleCharacterMatch()
    {
        $this->assertEqualsWithDelta(1.0, Jaro::distance("a", "a"), 1e-10);
    }
}