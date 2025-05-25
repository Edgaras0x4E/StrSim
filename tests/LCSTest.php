<?php

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use Edgaras\StrSim\LCS;

class LCSTest extends TestCase
{
    public function testIdenticalStrings()
    {
        $this->assertSame(6, LCS::length("abcdef", "abcdef"));
    }

    public function testCompletelyDifferentStrings()
    {
        $this->assertSame(0, LCS::length("abc", "xyz"));
    }

    public function testPartiallyMatchingStrings()
    {
        $this->assertSame(4, LCS::length("AGGTAB", "GXTXAYB")); 
    }

    public function testReorderedCharacters()
    {
        $this->assertSame(2, LCS::length("abc", "cab")); 
    }

    public function testSingleCharacterMatch()
    {
        $this->assertSame(1, LCS::length("a", "a"));
    }

    public function testSingleCharacterMismatch()
    {
        $this->assertSame(0, LCS::length("a", "b"));
    }

    public function testEmptyStrings()
    {
        $this->assertSame(0, LCS::length("", ""));
    }

    public function testOneEmptyString()
    {
        $this->assertSame(0, LCS::length("abc", ""));
        $this->assertSame(0, LCS::length("", "def"));
    }

    public function testLongerExample()
    {
        $a = "ABCBDAB";
        $b = "BDCAB";
        $this->assertSame(4, LCS::length($a, $b)); 
    }
}
