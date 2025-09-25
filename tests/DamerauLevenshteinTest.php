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

    public function testMultibyteIdenticalStrings()
    {
        $this->assertSame(0, DamerauLevenshtein::distance("café", "café"));
        $this->assertSame(0, DamerauLevenshtein::distance("🚀🌟", "🚀🌟"));
    }

    public function testMultibyteInsertion()
    {
        $this->assertSame(1, DamerauLevenshtein::distance("café", "caffé"));
        $this->assertSame(1, DamerauLevenshtein::distance("🚀", "🚀🌟"));
    }

    public function testMultibyteDeletion()
    {
        $this->assertSame(1, DamerauLevenshtein::distance("caffé", "café"));
        $this->assertSame(1, DamerauLevenshtein::distance("🚀🌟", "🚀"));
    }

    public function testMultibyteSubstitution()
    {
        $this->assertSame(1, DamerauLevenshtein::distance("café", "cafe"));
        $this->assertSame(1, DamerauLevenshtein::distance("🚀🌟", "🚀⭐"));
    }

    public function testMultibyteTransposition()
    {
        $this->assertSame(1, DamerauLevenshtein::distance("éö", "öé"));
        $this->assertSame(1, DamerauLevenshtein::distance("🚀🌟", "🌟🚀"));
    }

    public function testJapaneseCharacters()
    {
        $this->assertSame(1, DamerauLevenshtein::distance("こんにちは", "こんにちわ"));
        $this->assertSame(1, DamerauLevenshtein::distance("あい", "いあ"));
    }

    public function testCyrillicCharacters()
    {
        $this->assertSame(0, DamerauLevenshtein::distance("собака", "собака"));
    }

    public function testHebrewCharacters()
    {
        $this->assertSame(1, DamerauLevenshtein::distance("עברית", "עבדית"));
    }

    public function testMixedAsciiMultibyte()
    {
        $this->assertSame(1, DamerauLevenshtein::distance("hello café", "hello cafe"));
        $this->assertSame(1, DamerauLevenshtein::distance("test 🚀", "test 🌟"));
    }

    public function testMultibyteCompletelyDifferent()
    {
        $this->assertSame(4, DamerauLevenshtein::distance("café", "🚀🌟⭐"));
    }

    public function testMultibyteEmptyToNonEmpty()
    {
        $this->assertSame(4, DamerauLevenshtein::distance("", "café"));
        $this->assertSame(2, DamerauLevenshtein::distance("", "🚀🌟"));
    }

    public function testMultibyteNonEmptyToEmpty()
    {
        $this->assertSame(4, DamerauLevenshtein::distance("café", ""));
        $this->assertSame(2, DamerauLevenshtein::distance("🚀🌟", ""));
    }

    public function testSingleMultibyteCharacter()
    {
        $this->assertSame(0, DamerauLevenshtein::distance("é", "é"));
        $this->assertSame(1, DamerauLevenshtein::distance("é", "ö"));
        $this->assertSame(0, DamerauLevenshtein::distance("🚀", "🚀"));
    }

    public function testComplexMultibyteTranspositions()
    {
        $this->assertSame(3, DamerauLevenshtein::distance("éöü", "üé"));
    }
}
