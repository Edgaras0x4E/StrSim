<?php

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use Edgaras\StrSim\Jaro;
use Edgaras\StrSim\JaroWinkler;

class JaroWinklerTest extends TestCase
{
    public function testIdenticalStrings()
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("martha", "martha"), 1e-10);
    }

    public function testCompletelyDifferentStrings()
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("abc", "xyz"), 1e-10);
    }

    public function testKnownPairMARTHAvsMARHTA()
    {
        $jaro = Jaro::distance("martha", "marhta");  
        $prefix = 3;
        $expected = $jaro + $prefix * 0.1 * (1 - $jaro);
        $actual = JaroWinkler::distance("martha", "marhta");
        $this->assertEqualsWithDelta($expected, $actual, 1e-6);
    }

    public function testPrefixLimit()
    { 
        $a = "prefix_match_1";
        $b = "prefix_match_2";

        $jaro = Jaro::distance($a, $b);
        $expected = $jaro + 4 * 0.1 * (1 - $jaro);  
        $actual = JaroWinkler::distance($a, $b);
        $this->assertEqualsWithDelta($expected, $actual, 1e-6);
    }

    public function testNoCommonPrefix()
    {
        $a = "xxxxx";
        $b = "yyyyy";
        $jaro = Jaro::distance($a, $b);
        $expected = $jaro; // no prefix
        $this->assertEqualsWithDelta($expected, JaroWinkler::distance($a, $b), 1e-10);
    }

    public function testEmptyStrings()
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("", ""), 1e-10);
    }

    public function testOneEmptyString()
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("abc", ""), 1e-10);
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("", "xyz"), 1e-10);
    }

    public function testSingleCharMatch()
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("a", "a"), 1e-10);
    }

    public function testSingleCharMismatch()
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("a", "b"), 1e-10);
    }
}