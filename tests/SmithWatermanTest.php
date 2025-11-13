<?php

declare(strict_types=1);

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Edgaras\StrSim\SmithWaterman;

#[CoversClass(SmithWaterman::class)]
class SmithWatermanTest extends TestCase
{
    public function testIdenticalStrings(): void
    {
        $this->assertSame(14, SmithWaterman::score("GATTACA", "GATTACA"));
    }

    public function testCompletelyDifferentStrings(): void
    {
        $this->assertSame(0, SmithWaterman::score("AAAAAAA", "GGGGGGG"));
    }

    public function testPartialOverlap(): void
    {
        $this->assertSame(5, SmithWaterman::score("GATTACA", "GCATGCU"));
    }

    public function testEmptyStrings(): void
    {
        $this->assertSame(0, SmithWaterman::score("", ""));
    }

    public function testOneEmpty(): void
    {
        $this->assertSame(0, SmithWaterman::score("ACGT", ""));
        $this->assertSame(0, SmithWaterman::score("", "ACGT"));
    }

    public function testCustomScoring(): void
    {
        $this->assertSame(20, SmithWaterman::score("AAAA", "AAAA", match: 5, mismatch: -1, gap: -2));
    }

    public function testSubstringAlignment(): void
    {
        $this->assertSame(4, SmithWaterman::score("ACGT", "CG"));
    }

    public function testUtf8MultibyteCharacters(): void
    {
        $score = SmithWaterman::score("あ", "い");
        $this->assertSame(0, $score);
    }

    public function testInvalidUtf8Input(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $invalid = "\xFF\xFF";
        SmithWaterman::score($invalid, "test");
    }

}
