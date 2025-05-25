<?php

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use Edgaras\StrSim\DamerauLevenshtein;

class DamerauLevenshteinTest extends TestCase
{
    public function testIdenticalStrings()
    {
        $this->assertSame(0, DamerauLevenshtein::distance("test", "test"));
    }

    public function testInsertion()
    {
        $this->assertSame(1, DamerauLevenshtein::distance("test", "tests"));
    }

    public function testDeletion()
    {
        $this->assertSame(1, DamerauLevenshtein::distance("tests", "test"));
    }

    public function testSubstitution()
    {
        $this->assertSame(1, DamerauLevenshtein::distance("test", "tent"));
    }

    public function testTransposition()
    {
        $this->assertSame(1, DamerauLevenshtein::distance("ab", "ba"));
    }

    public function testComplexCase()
    {
        $this->assertSame(3, DamerauLevenshtein::distance("ca", "abc"));
    }

    public function testEmptyToNonEmpty()
    {
        $this->assertSame(4, DamerauLevenshtein::distance("", "test"));
    }

    public function testNonEmptyToEmpty()
    {
        $this->assertSame(4, DamerauLevenshtein::distance("test", ""));
    }

    public function testBothEmpty()
    {
        $this->assertSame(0, DamerauLevenshtein::distance("", ""));
    }
}
