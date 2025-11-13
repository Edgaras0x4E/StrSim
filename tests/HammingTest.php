<?php

declare(strict_types=1);

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Edgaras\StrSim\Hamming;

#[CoversClass(Hamming::class)]
class HammingTest extends TestCase
{
    public function testIdenticalStrings(): void
    {
        $this->assertSame(0, Hamming::distance("karolin", "karolin"));
    }

    public function testDifferentStrings(): void
    {
        $this->assertSame(3, Hamming::distance("karolin", "kathrin"));
        $this->assertSame(1, Hamming::distance("1011101", "1001101"));
        $this->assertSame(2, Hamming::distance("2173896", "2174890"));
    }

    public function testEmptyStrings(): void
    {
        $this->assertSame(0, Hamming::distance("", ""));
    }

    public function testThrowsOnUnequalLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Strings must be of equal length.");
        Hamming::distance("abc", "ab");
    }

    public function testThrowsOnOneEmptyOneNot(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Hamming::distance("", "a");
    }

    public function testMultibyteIdenticalStrings(): void
    {
        $this->assertSame(0, Hamming::distance("café", "café"));
        $this->assertSame(0, Hamming::distance("🚀🌟", "🚀🌟"));
    }

    public function testMultibyteDifferentStrings(): void
    {
        $this->assertSame(2, Hamming::distance("café", "case"));
        $this->assertSame(2, Hamming::distance("café", "casa"));
    }

    public function testEmojiDistance(): void
    {
        $this->assertSame(1, Hamming::distance("🚀🌟", "🚀⭐"));
        $this->assertSame(2, Hamming::distance("🚀🌟", "⭐🌙"));
    }

    public function testJapaneseCharacters(): void
    {
        $this->assertSame(1, Hamming::distance("こんにちは", "こんにちわ"));
        $this->assertSame(0, Hamming::distance("こんにちは", "こんにちは"));
    }

    public function testCyrillicCharacters(): void
    {
        $this->assertSame(4, Hamming::distance("собака", "корова"));
    }

    public function testHebrewCharacters(): void
    {
        $this->assertSame(1, Hamming::distance("עברית", "עבדית"));
    }

    public function testMixedAsciiMultibyte(): void
    {
        $this->assertSame(1, Hamming::distance("test é", "test e"));
    }

    public function testSingleMultibyteCharacter(): void
    {
        $this->assertSame(0, Hamming::distance("é", "é"));
        $this->assertSame(1, Hamming::distance("é", "ö"));
        $this->assertSame(0, Hamming::distance("🚀", "🚀"));
    }

    public function testMultibyteUnequalLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Strings must be of equal length.");
        Hamming::distance("café", "ca");
    }

    public function testMultibyteUnequalLengthEmoji(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Hamming::distance("🚀🌟", "🚀");
    }

    public function testGraphemeSkinToneUnequalLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Strings must be of equal length.");
        Hamming::distance("👍", "👍🏽");
    }

    public function testFlagRegionalIndicators(): void
    {
        $this->assertSame(1, Hamming::distance("🇺🇸", "🇺🇳"));
    }
}
