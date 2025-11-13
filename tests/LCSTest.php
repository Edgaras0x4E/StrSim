<?php

declare(strict_types=1);

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Edgaras\StrSim\LCS;

#[CoversClass(LCS::class)]
class LCSTest extends TestCase
{
    public function testIdenticalStrings(): void
    {
        $this->assertSame(6, LCS::length("abcdef", "abcdef"));
    }

    public function testCompletelyDifferentStrings(): void
    {
        $this->assertSame(0, LCS::length("abc", "xyz"));
    }

    public function testPartiallyMatchingStrings(): void
    {
        $this->assertSame(4, LCS::length("AGGTAB", "GXTXAYB"));
    }

    public function testReorderedCharacters(): void
    {
        $this->assertSame(2, LCS::length("abc", "cab"));
    }

    public function testSingleCharacterMatch(): void
    {
        $this->assertSame(1, LCS::length("a", "a"));
    }

    public function testSingleCharacterMismatch(): void
    {
        $this->assertSame(0, LCS::length("a", "b"));
    }

    public function testEmptyStrings(): void
    {
        $this->assertSame(0, LCS::length("", ""));
    }

    public function testOneEmptyString(): void
    {
        $this->assertSame(0, LCS::length("abc", ""));
        $this->assertSame(0, LCS::length("", "def"));
    }

    public function testLongerExample(): void
    {
        $a = "ABCBDAB";
        $b = "BDCAB";
        $this->assertSame(4, LCS::length($a, $b));
    }

    public function testMultibyteIdenticalStrings(): void
    {
        $this->assertSame(4, LCS::length("café", "café"));
        $this->assertSame(2, LCS::length("🚀🌟", "🚀🌟"));
    }

    public function testMultibyteCompletelyDifferent(): void
    {
        $this->assertSame(0, LCS::length("café", "🚀🌟"));
    }

    public function testMultibytePartialMatch(): void
    {
        $this->assertSame(3, LCS::length("café", "cafe"));
        $this->assertSame(1, LCS::length("🚀🌟", "🚀⭐"));
    }

    public function testJapaneseCharacters(): void
    {
        $this->assertSame(4, LCS::length("こんにちは", "こんにちわ"));
    }

    public function testCyrillicCharacters(): void
    {
        $this->assertSame(6, LCS::length("собака", "собака"));
    }

    public function testHebrewCharacters(): void
    {
        $this->assertSame(4, LCS::length("עברית", "עבדית"));
    }

    public function testMixedAsciiMultibyte(): void
    {
        $this->assertSame(9, LCS::length("hello café", "hello cafe"));
        $this->assertSame(5, LCS::length("test 🚀", "test 🌟"));
    }

    public function testMultibyteWithEmptyString(): void
    {
        $this->assertSame(0, LCS::length("café", ""));
        $this->assertSame(0, LCS::length("", "🚀🌟"));
    }

    public function testSingleMultibyteCharacter(): void
    {
        $this->assertSame(1, LCS::length("é", "é"));
        $this->assertSame(0, LCS::length("é", "ö"));
        $this->assertSame(1, LCS::length("🚀", "🚀"));
    }

    public function testMultibyteReorderedCharacters(): void
    {
        $this->assertSame(2, LCS::length("éöü", "üéö"));
    }

    public function testLongMultibyteStrings(): void
    {
        $longMb1 = str_repeat("ä", 100);
        $longMb2 = str_repeat("ö", 100);
        $this->assertSame(0, LCS::length($longMb1, $longMb2));

        $partialMatch = str_repeat("ä", 50) . str_repeat("ö", 50);
        $this->assertSame(50, LCS::length($longMb1, $partialMatch));
    }

    public function testNormalizationCombiningMarks(): void
    {
        $this->assertSame(0, LCS::length("é", "\u{0065}\u{0301}"));
    }

    public function testZWJFamilyEmojiLCS(): void
    {
        $this->assertSame(4, LCS::length("👨‍👩‍👧‍👦", "👨👩👧👦"));
    }
}
