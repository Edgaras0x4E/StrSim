<?php

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use Edgaras\StrSim\Hamming;

class HammingTest extends TestCase
{
    public function testIdenticalStrings()
    {
        $this->assertSame(0, Hamming::distance("karolin", "karolin"));
    }

    public function testDifferentStrings()
    {
        $this->assertSame(3, Hamming::distance("karolin", "kathrin"));
        $this->assertSame(1, Hamming::distance("1011101", "1001101"));
        $this->assertSame(2, Hamming::distance("2173896", "2174890"));
    }

    public function testEmptyStrings()
    {
        $this->assertSame(0, Hamming::distance("", ""));
    }

    public function testThrowsOnUnequalLength()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Strings must be of equal length.");
        Hamming::distance("abc", "ab");
    }

    public function testThrowsOnOneEmptyOneNot()
    {
        $this->expectException(\Exception::class);
        Hamming::distance("", "a");
    }
}
