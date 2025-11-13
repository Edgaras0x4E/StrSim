<?php

declare(strict_types=1);

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Edgaras\StrSim\DamerauLevenshtein;

#[CoversClass(DamerauLevenshtein::class)]
class DamerauLevenshteinTest extends TestCase
{
    public function testIdenticalStrings(): void
    {
        $this->assertSame(0, DamerauLevenshtein::distance("test", "test"));
    }

    public function testInsertion(): void
    {
        $this->assertSame(1, DamerauLevenshtein::distance("test", "tests"));
    }

    public function testDeletion(): void
    {
        $this->assertSame(1, DamerauLevenshtein::distance("tests", "test"));
    }

    public function testSubstitution(): void
    {
        $this->assertSame(1, DamerauLevenshtein::distance("test", "tent"));
    }

    public function testTransposition(): void
    {
        $this->assertSame(1, DamerauLevenshtein::distance("ab", "ba"));
    }

    public function testComplexCase(): void
    {
        $this->assertSame(3, DamerauLevenshtein::distance("ca", "abc"));
    }

    public function testEmptyToNonEmpty(): void
    {
        $this->assertSame(4, DamerauLevenshtein::distance("", "test"));
    }

    public function testNonEmptyToEmpty(): void
    {
        $this->assertSame(4, DamerauLevenshtein::distance("test", ""));
    }

    public function testBothEmpty(): void
    {
        $this->assertSame(0, DamerauLevenshtein::distance("", ""));
    }

    public function testMultibyteIdenticalStrings(): void
    {
        $this->assertSame(0, DamerauLevenshtein::distance("café", "café"));
        $this->assertSame(0, DamerauLevenshtein::distance("🚀🌟", "🚀🌟"));
    }

    public function testMultibyteInsertion(): void
    {
        $this->assertSame(1, DamerauLevenshtein::distance("café", "caffé"));
        $this->assertSame(1, DamerauLevenshtein::distance("🚀", "🚀🌟"));
    }

    public function testMultibyteDeletion(): void
    {
        $this->assertSame(1, DamerauLevenshtein::distance("caffé", "café"));
        $this->assertSame(1, DamerauLevenshtein::distance("🚀🌟", "🚀"));
    }

    public function testMultibyteSubstitution(): void
    {
        $this->assertSame(1, DamerauLevenshtein::distance("café", "cafe"));
        $this->assertSame(1, DamerauLevenshtein::distance("🚀🌟", "🚀⭐"));
    }

    public function testMultibyteTransposition(): void
    {
        $this->assertSame(1, DamerauLevenshtein::distance("éö", "öé"));
        $this->assertSame(1, DamerauLevenshtein::distance("🚀🌟", "🌟🚀"));
    }

    public function testJapaneseCharacters(): void
    {
        $this->assertSame(1, DamerauLevenshtein::distance("こんにちは", "こんにちわ"));
        $this->assertSame(1, DamerauLevenshtein::distance("あい", "いあ"));
    }

    public function testCyrillicCharacters(): void
    {
        $this->assertSame(0, DamerauLevenshtein::distance("собака", "собака"));
    }

    public function testHebrewCharacters(): void
    {
        $this->assertSame(1, DamerauLevenshtein::distance("עברית", "עבדית"));
    }

    public function testMixedAsciiMultibyte(): void
    {
        $this->assertSame(1, DamerauLevenshtein::distance("hello café", "hello cafe"));
        $this->assertSame(1, DamerauLevenshtein::distance("test 🚀", "test 🌟"));
    }

    public function testMultibyteCompletelyDifferent(): void
    {
        $this->assertSame(4, DamerauLevenshtein::distance("café", "🚀🌟⭐"));
    }

    public function testMultibyteEmptyToNonEmpty(): void
    {
        $this->assertSame(4, DamerauLevenshtein::distance("", "café"));
        $this->assertSame(2, DamerauLevenshtein::distance("", "🚀🌟"));
    }

    public function testMultibyteNonEmptyToEmpty(): void
    {
        $this->assertSame(4, DamerauLevenshtein::distance("café", ""));
        $this->assertSame(2, DamerauLevenshtein::distance("🚀🌟", ""));
    }

    public function testSingleMultibyteCharacter(): void
    {
        $this->assertSame(0, DamerauLevenshtein::distance("é", "é"));
        $this->assertSame(1, DamerauLevenshtein::distance("é", "ö"));
        $this->assertSame(0, DamerauLevenshtein::distance("🚀", "🚀"));
    }

    public function testComplexMultibyteTranspositions(): void
    {
        $this->assertSame(3, DamerauLevenshtein::distance("éöü", "üé"));
    }
}
