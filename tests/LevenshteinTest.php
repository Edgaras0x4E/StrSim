<?php

declare(strict_types=1);

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Edgaras\StrSim\Levenshtein;

#[CoversClass(Levenshtein::class)]
class LevenshteinTest extends TestCase
{
    public function testIdenticalStrings(): void
    {
        $this->assertSame(0, Levenshtein::distance("kitten", "kitten"));
    }

    public function testBasicDistance(): void
    {
        $this->assertSame(3, Levenshtein::distance("kitten", "sitting"));
        $this->assertSame(3, Levenshtein::distance("saturday", "sunday"));
    }

    public function testEmptyStrings(): void
    {
        $this->assertSame(0, Levenshtein::distance("", ""));
    }

    public function testOneEmptyString(): void
    {
        $this->assertSame(3, Levenshtein::distance("abc", ""));
        $this->assertSame(5, Levenshtein::distance("", "hello"));
    }

    public function testSingleCharacters(): void
    {
        $this->assertSame(0, Levenshtein::distance("a", "a"));
        $this->assertSame(1, Levenshtein::distance("a", "b"));
    }

    public function testCompletelyDifferentStrings(): void
    {
        $this->assertSame(3, Levenshtein::distance("abc", "xyz"));
    }

    public function testInsertionDeletion(): void
    {
        $this->assertSame(1, Levenshtein::distance("abc", "ab"));
        $this->assertSame(1, Levenshtein::distance("ab", "abc"));
    }

    public function testSubstitution(): void
    {
        $this->assertSame(1, Levenshtein::distance("abc", "axc"));
    }

    public function testMultibyteIdentical(): void
    {
        $this->assertSame(0, Levenshtein::distance("café", "café"));
        $this->assertSame(0, Levenshtein::distance("こんにちは", "こんにちは"));
    }

    public function testMultibyteDistance(): void
    {
        $this->assertSame(1, Levenshtein::distance("café", "cafe"));
        $this->assertSame(1, Levenshtein::distance("naïve", "naive"));
    }

    public function testEmojiSupport(): void
    {
        $this->assertSame(0, Levenshtein::distance("🚀🌟", "🚀🌟"));
        $this->assertSame(1, Levenshtein::distance("🚀🌟", "🚀⭐"));
        $this->assertSame(2, Levenshtein::distance("🚀🌟", "⭐🌙"));
    }

    public function testJapaneseCharacters(): void
    {
        $this->assertSame(1, Levenshtein::distance("こんにちは", "こんにちわ"));
        $this->assertSame(5, Levenshtein::distance("こんにちは", "さようなら"));
    }

    public function testCyrillicCharacters(): void
    {
        $this->assertSame(1, Levenshtein::distance("собака", "собаки"));
        $this->assertSame(7, Levenshtein::distance("собака", "медведь"));
    }

    public function testHebrewCharacters(): void
    {
        $this->assertSame(1, Levenshtein::distance("עברית", "עבדית"));
    }

    public function testMixedAsciiMultibyte(): void
    {
        $this->assertSame(1, Levenshtein::distance("hello café", "hello cafe"));
        $this->assertSame(1, Levenshtein::distance("test 🚀", "test 🌟"));
    }

    public function testLongStrings(): void
    {
        $longString1 = str_repeat("a", 1000);
        $longString2 = str_repeat("b", 1000);
        $this->assertSame(1000, Levenshtein::distance($longString1, $longString2));
    }

    public function testLongMultibyteStrings(): void
    {
        $longMb1 = str_repeat("ä", 100);
        $longMb2 = str_repeat("ö", 100);
        $this->assertSame(100, Levenshtein::distance($longMb1, $longMb2));
    }

    public function testNormalizationCombiningMarks(): void
    {
        $this->assertSame(2, Levenshtein::distance("é", "\u{0065}\u{0301}"));
    }

    public function testNFKCCompatibilityNormalization(): void
    {
        $this->assertSame(1, Levenshtein::distance("①", "1"));
        $this->assertSame(2, Levenshtein::distance("ﬀ", "ff"));
        $this->assertSame(1, Levenshtein::distance("Å", "Å"));
    }

    public function testNFKDDecomposedNormalization(): void
    {
        $this->assertSame(2, Levenshtein::distance("Å", "A\u{030A}"));
        $this->assertSame(2, Levenshtein::distance("ñ", "n\u{0303}"));
    }

    public function testZWJFamilyEmojiDistance(): void
    {
        $this->assertSame(3, Levenshtein::distance("👨‍👩‍👧‍👦", "👨👩👧👦"));
    }

    public function testSkinToneModifierDistance(): void
    {
        $this->assertSame(1, Levenshtein::distance("👍", "👍🏽"));
    }
}
