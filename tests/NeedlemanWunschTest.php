<?php

declare(strict_types=1);

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Edgaras\StrSim\NeedlemanWunsch;

#[CoversClass(NeedlemanWunsch::class)]
class NeedlemanWunschTest extends TestCase
{
    public function testIdenticalStrings(): void
    {
        $this->assertSame(7, NeedlemanWunsch::score("GATTACA", "GATTACA"));
    }

    public function testCompletelyDifferentStrings(): void
    {
        $this->assertSame(-7, NeedlemanWunsch::score("AAAAAAA", "GGGGGGG"));
    }

    public function testPartialMatch(): void
    {
        $this->assertSame(0, NeedlemanWunsch::score("GATTACA", "GCATGCU"));
    }

    public function testEmptyStrings(): void
    {
        $this->assertSame(0, NeedlemanWunsch::score("", ""));
    }

    public function testOneEmpty(): void
    {
        $this->assertSame(-4, NeedlemanWunsch::score("ACGT", ""));
        $this->assertSame(-4, NeedlemanWunsch::score("", "ACGT"));
    }

    public function testCustomScoring(): void
    {
        $this->assertSame(20, NeedlemanWunsch::score("AAAA", "AAAA", match: 5, mismatch: -1, gap: -2));
    }

    public function testGapOnly(): void
    {
        $this->assertSame(-1, NeedlemanWunsch::score("A", "AAA"));
    }

    public function testValidUtf8Passes(): void
    {
        $this->assertSame(3, NeedlemanWunsch::score("ありがとう", "ありがと"));
    }

    public function testInvalidUtf8Input(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $invalid = "\xFF\xFF";
        NeedlemanWunsch::score($invalid, "test");
    }

}
