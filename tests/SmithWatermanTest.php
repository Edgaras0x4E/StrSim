<?php

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use Edgaras\StrSim\SmithWaterman;

class SmithWatermanTest extends TestCase
{
    public function testIdenticalStrings()
    { 
        $this->assertSame(14, SmithWaterman::score("GATTACA", "GATTACA"));
    }

    public function testCompletelyDifferentStrings()
    { 
        $this->assertSame(0, SmithWaterman::score("AAAAAAA", "GGGGGGG"));
    }

    public function testPartialOverlap()
    {
        $this->assertSame(5, SmithWaterman::score("GATTACA", "GCATGCU"));
    }

    public function testEmptyStrings()
    {
        $this->assertSame(0, SmithWaterman::score("", ""));
    }

    public function testOneEmpty()
    {
        $this->assertSame(0, SmithWaterman::score("ACGT", ""));
        $this->assertSame(0, SmithWaterman::score("", "ACGT"));
    }

    public function testCustomScoring()
    { 
        $this->assertSame(20, SmithWaterman::score("AAAA", "AAAA", match: 5, mismatch: -1, gap: -2));
    }

    public function testCustomScoringWithMismatchAndGaps()
    {
        $this->assertSame(13, SmithWaterman::score("TGTTACGG", "GGTTGACTA", match: 3, mismatch: -3, gap: -2));
        $this->assertSame(4, SmithWaterman::score("GATTACA", "GCATGCU", match: 2, mismatch: -3, gap: -2));
        $this->assertSame(3, SmithWaterman::score("ABD", "ACD", match: 2, mismatch: -1, gap: -5));
        $this->assertSame(2, SmithWaterman::score("ABD", "ACD", match: 2, mismatch: -3, gap: -5));
        $this->assertSame(4, SmithWaterman::score("ACGT", "AT", match: 3, mismatch: -1, gap: -1));
        $this->assertSame(3, SmithWaterman::score("ACGT", "AT", match: 3, mismatch: -1, gap: -2));
        $this->assertSame(3, SmithWaterman::score("AT", "ACGT", match: 3, mismatch: -1, gap: -2));
    }

    public function testSubstringAlignment()
    { 
        $this->assertSame(4, SmithWaterman::score("ACGT", "CG"));
    }

    public function testUtf8MultibyteCharacters()
    { 
        $score = SmithWaterman::score("あ", "い");
        $this->assertSame(0, $score);
    }

    public function testInvalidUtf8Input()
    {
        $this->expectException(\InvalidArgumentException::class);
        $invalid = "\xFF\xFF";
        SmithWaterman::score($invalid, "test");
    }

}
