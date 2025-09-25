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

    public function testMultibyteIdenticalStrings()
    {
        $this->assertSame(8, SmithWaterman::score("café", "café"));
        $this->assertSame(4, SmithWaterman::score("🚀🌟", "🚀🌟"));
    }

    public function testMultibytePartialMatch()
    {
        $result = SmithWaterman::score("café", "cafe");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(8, $result);
    }

    public function testMultibyteCompletelyDifferent()
    {
        $this->assertSame(0, SmithWaterman::score("café", "🚀🌟"));
    }

    public function testEmojiAlignment()
    {
        $this->assertSame(4, SmithWaterman::score("🚀🌟", "🚀🌟"));
        $result = SmithWaterman::score("🚀🌟", "🚀⭐");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(4, $result);
    }

    public function testJapaneseAlignment()
    {
        $result = SmithWaterman::score("こんにちは", "こんにちわ");
        $this->assertGreaterThan(0, $result);
    }

    public function testCyrillicAlignment()
    {
        $result = SmithWaterman::score("собака", "собаки");
        $this->assertGreaterThan(0, $result);
    }

    public function testHebrewAlignment()
    {
        $result = SmithWaterman::score("עברית", "עבדית");
        $this->assertGreaterThan(0, $result);
    }

    public function testMixedAsciiMultibyte()
    {
        $result = SmithWaterman::score("hello café", "hello cafe");
        $this->assertGreaterThan(0, $result);
    }

    public function testMultibyteWithEmptyString()
    {
        $this->assertSame(0, SmithWaterman::score("café", ""));
        $this->assertSame(0, SmithWaterman::score("🚀🌟", ""));
    }

    public function testSingleMultibyteCharacter()
    {
        $this->assertSame(2, SmithWaterman::score("é", "é"));
        $this->assertSame(0, SmithWaterman::score("é", "ö"));
        $this->assertSame(2, SmithWaterman::score("🚀", "🚀"));
    }

    public function testMultibyteCustomScoring()
    {
        $score = SmithWaterman::score("café", "café", match: 3, mismatch: -2, gap: -1);
        $this->assertSame(12, $score);
    }

    public function testMultibyteSubstringAlignment()
    {
        $result = SmithWaterman::score("café latte", "café");
        $this->assertGreaterThan(0, $result);
    }

    public function testSmallMatrixBoundaryConditions()
    {
        $this->assertSame(2, SmithWaterman::score("A", "A"));
        $this->assertSame(0, SmithWaterman::score("A", "B"));
        $this->assertSame(4, SmithWaterman::score("AB", "AB"));
        $this->assertSame(2, SmithWaterman::score("AB", "AC"));
        $this->assertSame(0, SmithWaterman::score("AB", "CD"));
        $this->assertSame(2, SmithWaterman::score("A", "AB"));
        $this->assertSame(2, SmithWaterman::score("AB", "A"));
    }

    public function testFirstRowColumnInitialization()
    {
        $this->assertSame(0, SmithWaterman::score("", "A"));
        $this->assertSame(0, SmithWaterman::score("A", ""));
        $this->assertSame(0, SmithWaterman::score("", ""));
    }

    public function testZeroResetProperty()
    {
        $this->assertSame(0, SmithWaterman::score("AAAA", "GGGG"));
        $this->assertSame(0, SmithWaterman::score("A", "G"));
    }

    public function testCustomScoringBoundaries()
    {
        $this->assertSame(50, SmithWaterman::score("A", "A", match: 50, mismatch: -10, gap: -5));
        $this->assertSame(0, SmithWaterman::score("A", "B", match: 50, mismatch: -10, gap: -5));
    }

}
