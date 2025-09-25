<?php

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use Edgaras\StrSim\LCS;

class LCSTest extends TestCase
{
    public function testIdenticalStrings()
    {
        $this->assertSame(6, LCS::length("abcdef", "abcdef"));
    }

    public function testCompletelyDifferentStrings()
    {
        $this->assertSame(0, LCS::length("abc", "xyz"));
    }

    public function testPartiallyMatchingStrings()
    {
        $this->assertSame(4, LCS::length("AGGTAB", "GXTXAYB")); 
    }

    public function testReorderedCharacters()
    {
        $this->assertSame(2, LCS::length("abc", "cab")); 
    }

    public function testSingleCharacterMatch()
    {
        $this->assertSame(1, LCS::length("a", "a"));
    }

    public function testSingleCharacterMismatch()
    {
        $this->assertSame(0, LCS::length("a", "b"));
    }

    public function testEmptyStrings()
    {
        $this->assertSame(0, LCS::length("", ""));
    }

    public function testOneEmptyString()
    {
        $this->assertSame(0, LCS::length("abc", ""));
        $this->assertSame(0, LCS::length("", "def"));
    }

    public function testLongerExample()
    {
        $a = "ABCBDAB";
        $b = "BDCAB";
        $this->assertSame(4, LCS::length($a, $b)); 
    }

    public function testMultibyteIdenticalStrings()
    {
        $this->assertSame(4, LCS::length("café", "café"));
        $this->assertSame(2, LCS::length("🚀🌟", "🚀🌟"));
    }

    public function testMultibyteCompletelyDifferent()
    {
        $this->assertSame(0, LCS::length("café", "🚀🌟"));
    }

    public function testMultibytePartialMatch()
    {
        $this->assertSame(3, LCS::length("café", "cafe"));
        $this->assertSame(1, LCS::length("🚀🌟", "🚀⭐"));
    }

    public function testJapaneseCharacters()
    {
        $this->assertSame(4, LCS::length("こんにちは", "こんにちわ"));
    }

    public function testCyrillicCharacters()
    {
        $this->assertSame(6, LCS::length("собака", "собака"));
    }

    public function testHebrewCharacters()
    {
        $this->assertSame(4, LCS::length("עברית", "עבדית"));
    }

    public function testMixedAsciiMultibyte()
    {
        $this->assertSame(9, LCS::length("hello café", "hello cafe"));
        $this->assertSame(5, LCS::length("test 🚀", "test 🌟"));
    }

    public function testMultibyteWithEmptyString()
    {
        $this->assertSame(0, LCS::length("café", ""));
        $this->assertSame(0, LCS::length("", "🚀🌟"));
    }

    public function testSingleMultibyteCharacter()
    {
        $this->assertSame(1, LCS::length("é", "é"));
        $this->assertSame(0, LCS::length("é", "ö"));
        $this->assertSame(1, LCS::length("🚀", "🚀"));
    }

    public function testMultibyteReorderedCharacters()
    {
        $this->assertSame(2, LCS::length("éöü", "üéö"));
    }

    public function testLongMultibyteStrings()
    {
        $longMb1 = str_repeat("ä", 100);
        $longMb2 = str_repeat("ö", 100);
        $this->assertSame(0, LCS::length($longMb1, $longMb2));
        
        $partialMatch = str_repeat("ä", 50) . str_repeat("ö", 50);
        $this->assertSame(50, LCS::length($longMb1, $partialMatch));
    }

    public function testNormalizationCombiningMarks()
    {
        $this->assertSame(0, LCS::length("é", "\u{0065}\u{0301}"));
    }

    public function testZWJFamilyEmojiLCS()
    {
        $this->assertSame(4, LCS::length("👨‍👩‍👧‍👦", "👨👩👧👦"));
    }
}
