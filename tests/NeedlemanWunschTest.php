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

    public function testValidUtf8Passes()
    {
        $this->assertSame(3, NeedlemanWunsch::score("ありがとう", "ありがと"));
    }

    public function testInvalidUtf8Input()
    {
        $this->expectException(\InvalidArgumentException::class);
        $invalid = "\xFF\xFF";
        NeedlemanWunsch::score($invalid, "test");
    }

    public function testMultibyteIdenticalStrings()
    {
        $this->assertSame(4, NeedlemanWunsch::score("café", "café"));
        $this->assertSame(2, NeedlemanWunsch::score("🚀🌟", "🚀🌟"));
    }

    public function testMultibytePartialMatch()
    {
        $result = NeedlemanWunsch::score("café", "cafe");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(4, $result);
    }

    public function testMultibyteCompletelyDifferent()
    {
        $result = NeedlemanWunsch::score("café", "🚀🌟");
        $this->assertLessThan(0, $result);
    }

    public function testEmojiAlignment()
    {
        $this->assertSame(2, NeedlemanWunsch::score("🚀🌟", "🚀🌟"));
        $result = NeedlemanWunsch::score("🚀🌟", "🚀⭐");
        $this->assertGreaterThanOrEqual(0, $result);
        $this->assertLessThan(2, $result);
    }

    public function testJapaneseAlignment()
    {
        $result = NeedlemanWunsch::score("こんにちは", "こんにちわ");
        $this->assertGreaterThan(0, $result);
    }

    public function testCyrillicAlignment()
    {
        $result = NeedlemanWunsch::score("собака", "собаки");
        $this->assertGreaterThan(0, $result);
    }

    public function testHebrewAlignment()
    {
        $result = NeedlemanWunsch::score("עברית", "עבדית");
        $this->assertGreaterThan(0, $result);
    }

    public function testMixedAsciiMultibyte()
    {
        $result = NeedlemanWunsch::score("hello café", "hello cafe");
        $this->assertGreaterThan(0, $result);
    }

    public function testMultibyteWithEmptyString()
    {
        $this->assertSame(-4, NeedlemanWunsch::score("café", ""));
        $this->assertSame(-2, NeedlemanWunsch::score("🚀🌟", ""));
    }

    public function testSingleMultibyteCharacter()
    {
        $this->assertSame(1, NeedlemanWunsch::score("é", "é"));
        $this->assertSame(-1, NeedlemanWunsch::score("é", "ö"));
        $this->assertSame(1, NeedlemanWunsch::score("🚀", "🚀"));
    }

    public function testMultibyteCustomScoring()
    {
        $score = NeedlemanWunsch::score("café", "café", match: 3, mismatch: -2, gap: -1);
        $this->assertSame(12, $score);
    }

    public function testSmallMatrixBoundaryConditions()
    {
        $this->assertSame(1, NeedlemanWunsch::score("A", "A"));
        $this->assertSame(-1, NeedlemanWunsch::score("A", "B"));
        $this->assertSame(2, NeedlemanWunsch::score("AB", "AB"));
        $this->assertSame(0, NeedlemanWunsch::score("AB", "AC"));
        $this->assertSame(-2, NeedlemanWunsch::score("AB", "CD"));
        $this->assertSame(0, NeedlemanWunsch::score("A", "AB"));
        $this->assertSame(0, NeedlemanWunsch::score("AB", "A"));
    }

    public function testFirstRowColumnInitialization()
    {
        $this->assertSame(-1, NeedlemanWunsch::score("", "A"));
        $this->assertSame(-1, NeedlemanWunsch::score("A", ""));
        $this->assertSame(0, NeedlemanWunsch::score("", ""));
        $this->assertSame(-5, NeedlemanWunsch::score("", "A", gap: -5));
        $this->assertSame(-5, NeedlemanWunsch::score("A", "", gap: -5));
    }

    public function testGlobalAlignmentProperty()
    {
        $this->assertSame(-1, NeedlemanWunsch::score("A", "B"));
        $this->assertSame(0, NeedlemanWunsch::score("A", "AB"));
        $this->assertSame(0, NeedlemanWunsch::score("AB", "A"));
    }

    public function testCustomScoringBoundaries()
    {
        $this->assertSame(50, NeedlemanWunsch::score("A", "A", match: 50, mismatch: -10, gap: -5));
        $this->assertSame(-10, NeedlemanWunsch::score("A", "B", match: 50, mismatch: -10, gap: -5));
    }

}
