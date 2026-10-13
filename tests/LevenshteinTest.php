<?php

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use Edgaras\StrSim\Levenshtein;

class LevenshteinTest extends TestCase
{
    public function testIdenticalStrings()
    {
        $this->assertSame(0, Levenshtein::distance("kitten", "kitten"));
    }

    public function testBasicDistance()
    {
        $this->assertSame(3, Levenshtein::distance("kitten", "sitting"));
        $this->assertSame(3, Levenshtein::distance("saturday", "sunday"));
    }

    public function testEmptyStrings()
    {
        $this->assertSame(0, Levenshtein::distance("", ""));
    }

    public function testOneEmptyString()
    {
        $this->assertSame(3, Levenshtein::distance("abc", ""));
        $this->assertSame(5, Levenshtein::distance("", "hello"));
    }

    public function testSingleCharacters()
    {
        $this->assertSame(0, Levenshtein::distance("a", "a"));
        $this->assertSame(1, Levenshtein::distance("a", "b"));
    }

    public function testCompletelyDifferentStrings()
    {
        $this->assertSame(3, Levenshtein::distance("abc", "xyz"));
    }

    public function testInsertionDeletion()
    {
        $this->assertSame(1, Levenshtein::distance("abc", "ab"));
        $this->assertSame(1, Levenshtein::distance("ab", "abc"));
    }

    public function testSubstitution()
    {
        $this->assertSame(1, Levenshtein::distance("abc", "axc"));
    }

    public function testMultibyteIdentical()
    {
        $this->assertSame(0, Levenshtein::distance("café", "café"));
        $this->assertSame(0, Levenshtein::distance("こんにちは", "こんにちは"));
    }

    public function testMultibyteDistance()
    {
        $this->assertSame(1, Levenshtein::distance("café", "cafe"));
        $this->assertSame(1, Levenshtein::distance("naïve", "naive"));
    }

    public function testEmojiSupport()
    {
        $this->assertSame(0, Levenshtein::distance("🚀🌟", "🚀🌟"));
        $this->assertSame(1, Levenshtein::distance("🚀🌟", "🚀⭐"));
        $this->assertSame(2, Levenshtein::distance("🚀🌟", "⭐🌙"));
    }

    public function testJapaneseCharacters()
    {
        $this->assertSame(1, Levenshtein::distance("こんにちは", "こんにちわ"));
        $this->assertSame(5, Levenshtein::distance("こんにちは", "さようなら"));
    }

    public function testCyrillicCharacters()
    {
        $this->assertSame(1, Levenshtein::distance("собака", "собаки"));
        $this->assertSame(7, Levenshtein::distance("собака", "медведь"));
    }

    public function testHebrewCharacters()
    {
        $this->assertSame(1, Levenshtein::distance("עברית", "עבדית"));
    }

    public function testMixedAsciiMultibyte()
    {
        $this->assertSame(1, Levenshtein::distance("hello café", "hello cafe"));
        $this->assertSame(1, Levenshtein::distance("test 🚀", "test 🌟"));
    }

    public function testLongStrings()
    {
        $longString1 = str_repeat("a", 1000);
        $longString2 = str_repeat("b", 1000);
        $this->assertSame(1000, Levenshtein::distance($longString1, $longString2));
    }

    public function testLongMultibyteStrings()
    {
        $longMb1 = str_repeat("ä", 100);
        $longMb2 = str_repeat("ö", 100);
        $this->assertSame(100, Levenshtein::distance($longMb1, $longMb2));
    }

    public function testNormalizationCombiningMarks()
    {
        $this->assertSame(2, Levenshtein::distance("é", "\u{0065}\u{0301}"));
    }

    public function testNFKCCompatibilityNormalization()
    {
        $this->assertSame(1, Levenshtein::distance("①", "1"));
        $this->assertSame(2, Levenshtein::distance("ﬀ", "ff"));
        $this->assertSame(1, Levenshtein::distance("Å", "Å"));
    }

    public function testNFKDDecomposedNormalization()
    {
        $this->assertSame(2, Levenshtein::distance("Å", "A\u{030A}"));
        $this->assertSame(2, Levenshtein::distance("ñ", "n\u{0303}"));
    }

    public function testZWJFamilyEmojiDistance()
    {
        $this->assertSame(3, Levenshtein::distance("👨‍👩‍👧‍👦", "👨👩👧👦"));
    }

    public function testSkinToneModifierDistance()
    {
        $this->assertSame(1, Levenshtein::distance("👍", "👍🏽"));
    }

    public function testSymmetry()
    {
        $pairs = [["kitten", "sitting"], ["", "abc"], ["café", "caffe"], ["🚀🌟", "⭐"], ["flaw", "lawn"]];
        foreach ($pairs as [$a, $b]) {
            $this->assertSame(Levenshtein::distance($a, $b), Levenshtein::distance($b, $a));
        }
    }

    public function testMatchesBuiltinLevenshteinOnAscii()
    {
        mt_srand(42);
        for ($k = 0; $k < 200; $k++) {
            $a = substr(str_shuffle(str_repeat('abcd', 4)), 0, mt_rand(0, 12));
            $b = substr(str_shuffle(str_repeat('abcd', 4)), 0, mt_rand(0, 12));
            $this->assertSame(levenshtein($a, $b), Levenshtein::distance($a, $b), "'$a' vs '$b'");
        }
    }
}
