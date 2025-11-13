<?php

declare(strict_types=1);

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Edgaras\StrSim\Jaccard;

#[CoversClass(Jaccard::class)]
class JaccardTest extends TestCase
{
    public function testIdenticalStrings(): void
    {
        $this->assertEqualsWithDelta(1.0, Jaccard::index("abc", "abc"), 1e-10);
    }

    public function testCompletelyDifferentStrings(): void
    {
        $this->assertEqualsWithDelta(0.0, Jaccard::index("abc", "xyz"), 1e-10);
    }

    public function testPartiallyOverlappingStrings(): void
    {
        $this->assertEqualsWithDelta(0.5, Jaccard::index("abc", "bcd"), 1e-10);
    }

    public function testEmptyStrings(): void
    {
        $this->assertEqualsWithDelta(0.0, Jaccard::index("", ""), 1e-10);
    }

    public function testOneEmptyString(): void
    {
        $this->assertEqualsWithDelta(0.0, Jaccard::index("abc", ""), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaccard::index("", "xyz"), 1e-10);
    }

    public function testRepeatedCharacters(): void
    {
        $this->assertEqualsWithDelta(1.0, Jaccard::index("aaaa", "a"), 1e-10);
    }

    public function testMultibyteIdenticalStrings(): void
    {
        $this->assertEqualsWithDelta(1.0, Jaccard::index("café", "café"), 1e-10);
        $this->assertEqualsWithDelta(1.0, Jaccard::index("🚀🌟", "🚀🌟"), 1e-10);
    }

    public function testMultibyteDifferentStrings(): void
    {
        $this->assertEqualsWithDelta(0.0, Jaccard::index("café", "🚀🌟"), 1e-10);
    }

    public function testMultibytePartialOverlap(): void
    {
        $result = Jaccard::index("café", "caffé");
        $this->assertEqualsWithDelta(1.0, $result, 1e-10);
    }

    public function testJapaneseCharacters(): void
    {
        $result = Jaccard::index("こんにちは", "こんにちわ");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testEmojiSupport(): void
    {
        $this->assertEqualsWithDelta(0.3333333333333333, Jaccard::index("🚀🌟", "🚀⭐"), 1e-10);
    }

    public function testCyrillicCharacters(): void
    {
        $result = Jaccard::index("собака", "собаки");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testHebrewCharacters(): void
    {
        $result = Jaccard::index("עברית", "עבדית");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testMixedAsciiMultibyte(): void
    {
        $result = Jaccard::index("hello café", "hello cafe");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testMultibyteWithEmptyString(): void
    {
        $this->assertEqualsWithDelta(0.0, Jaccard::index("café", ""), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaccard::index("", "🚀🌟"), 1e-10);
    }

    public function testSingleMultibyteCharacter(): void
    {
        $this->assertEqualsWithDelta(1.0, Jaccard::index("é", "é"), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaccard::index("é", "ö"), 1e-10);
        $this->assertEqualsWithDelta(1.0, Jaccard::index("🚀", "🚀"), 1e-10);
    }

    public function testMultibyteRepeatedCharacters(): void
    {
        $this->assertEqualsWithDelta(1.0, Jaccard::index("éééé", "é"), 1e-10);
    }

    public function testNormalizationCombiningMarks(): void
    {
        $this->assertEqualsWithDelta(0.0, Jaccard::index("é", "\u{0065}\u{0301}"), 1e-10);
    }

    public function testZWJFamilyEmojiSetOverlap(): void
    {
        $this->assertEqualsWithDelta(0.8, Jaccard::index("👨‍👩‍👧‍👦", "👨👩👧👦"), 1e-10);
    }

    public function testNFKCCompatibilityNormalization(): void
    {
        $this->assertEqualsWithDelta(0.0, Jaccard::index("①", "1"), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaccard::index("ﬀ", "ff"), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaccard::index("Å", "Å"), 1e-10);
    }

    public function testNFKDDecomposedNormalization(): void
    {
        $this->assertEqualsWithDelta(0.0, Jaccard::index("Å", "A\u{030A}"), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaccard::index("ñ", "n\u{0303}"), 1e-10);
    }

    public function testInvalidUtf8Input(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Input strings must be valid UTF-8.");
        $invalid = "\xFF\xFF";
        Jaccard::index($invalid, "test");
    }
}
