<?php

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use Edgaras\StrSim\Hamming;

class HammingTest extends TestCase
{
    public function testIdenticalStrings()
    {
        $this->assertSame(0, Hamming::distance("karolin", "karolin"));
    }

    public function testDifferentStrings()
    {
        $this->assertSame(3, Hamming::distance("karolin", "kathrin"));
        $this->assertSame(1, Hamming::distance("1011101", "1001101"));
        $this->assertSame(2, Hamming::distance("2173896", "2174890"));
    }

    public function testEmptyStrings()
    {
        $this->assertSame(0, Hamming::distance("", ""));
    }

    public function testThrowsOnUnequalLength()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Strings must be of equal length.");
        Hamming::distance("abc", "ab");
    }

    public function testThrowsOnOneEmptyOneNot()
    {
        $this->expectException(\InvalidArgumentException::class);
        Hamming::distance("", "a");
    }

    public function testMultibyteIdenticalStrings()
    {
        $this->assertSame(0, Hamming::distance("café", "café"));
        $this->assertSame(0, Hamming::distance("🚀🌟", "🚀🌟"));
    }

    public function testMultibyteDifferentStrings()
    {
        $this->assertSame(2, Hamming::distance("café", "case"));
        $this->assertSame(2, Hamming::distance("café", "casa"));
    }

    public function testEmojiDistance()
    {
        $this->assertSame(1, Hamming::distance("🚀🌟", "🚀⭐"));
        $this->assertSame(2, Hamming::distance("🚀🌟", "⭐🌙"));
    }

    public function testJapaneseCharacters()
    {
        $this->assertSame(1, Hamming::distance("こんにちは", "こんにちわ"));
        $this->assertSame(0, Hamming::distance("こんにちは", "こんにちは"));
    }

    public function testCyrillicCharacters()
    {
        $this->assertSame(4, Hamming::distance("собака", "корова"));
    }

    public function testHebrewCharacters()
    {
        $this->assertSame(1, Hamming::distance("עברית", "עבדית"));
    }

    public function testMixedAsciiMultibyte()
    {
        $this->assertSame(1, Hamming::distance("test é", "test e"));
    }

    public function testSingleMultibyteCharacter()
    {
        $this->assertSame(0, Hamming::distance("é", "é"));
        $this->assertSame(1, Hamming::distance("é", "ö"));
        $this->assertSame(0, Hamming::distance("🚀", "🚀"));
    }

    public function testMultibyteUnequalLength()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Strings must be of equal length.");
        Hamming::distance("café", "ca");
    }

    public function testMultibyteUnequalLengthEmoji()
    {
        $this->expectException(\InvalidArgumentException::class);
        Hamming::distance("🚀🌟", "🚀");
    }

    public function testGraphemeSkinToneUnequalLength()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Strings must be of equal length.");
        Hamming::distance("👍", "👍🏽");
    }

    public function testFlagRegionalIndicators()
    {
        $this->assertSame(1, Hamming::distance("🇺🇸", "🇺🇳"));
    }
}
