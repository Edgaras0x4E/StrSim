<?php

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use Edgaras\StrSim\NeedlemanWunsch;

class NeedlemanWunschTest extends TestCase
{
    public function testIdenticalStrings()
    { 
        $this->assertSame(7, NeedlemanWunsch::score("GATTACA", "GATTACA"));
    }

    public function testCompletelyDifferentStrings()
    { 
        $this->assertSame(-7, NeedlemanWunsch::score("AAAAAAA", "GGGGGGG"));
    }

    public function testPartialMatch()
    { 
        $this->assertSame(0, NeedlemanWunsch::score("GATTACA", "GCATGCU"));
    }

    public function testEmptyStrings()
    {
        $this->assertSame(0, NeedlemanWunsch::score("", ""));
    }

    public function testOneEmpty()
    { 
        $this->assertSame(-4, NeedlemanWunsch::score("ACGT", ""));
        $this->assertSame(-4, NeedlemanWunsch::score("", "ACGT"));
    }

    public function testCustomScoring()
    { 
        $this->assertSame(20, NeedlemanWunsch::score("AAAA", "AAAA", match: 5, mismatch: -1, gap: -2));
    }

    public function testGapOnly()
    { 
        $this->assertSame(-1, NeedlemanWunsch::score("A", "AAA"));
    }
}
