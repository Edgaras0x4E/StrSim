<?php

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use Edgaras\StrSim\Jaccard;

class JaccardTest extends TestCase
{
    public function testIdenticalStrings()
    {
        $this->assertEqualsWithDelta(1.0, Jaccard::index("abc", "abc"), 1e-10);
    }

    public function testCompletelyDifferentStrings()
    {
        $this->assertEqualsWithDelta(0.0, Jaccard::index("abc", "xyz"), 1e-10);
    }

    public function testPartiallyOverlappingStrings()
    {
        $this->assertEqualsWithDelta(0.5, Jaccard::index("abc", "bcd"), 1e-10);
    }

    public function testEmptyStrings()
    {
        $this->assertEqualsWithDelta(0.0, Jaccard::index("", ""), 1e-10);
    }

    public function testOneEmptyString()
    {
        $this->assertEqualsWithDelta(0.0, Jaccard::index("abc", ""), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaccard::index("", "xyz"), 1e-10);
    }

    public function testRepeatedCharacters()
    {
        $this->assertEqualsWithDelta(1.0, Jaccard::index("aaaa", "a"), 1e-10);
    }

    public function testMultibyteIdenticalStrings()
    {
        $this->assertEqualsWithDelta(1.0, Jaccard::index("café", "café"), 1e-10);
        $this->assertEqualsWithDelta(1.0, Jaccard::index("🚀🌟", "🚀🌟"), 1e-10);
    }

    public function testMultibyteDifferentStrings()
    {
        $this->assertEqualsWithDelta(0.0, Jaccard::index("café", "🚀🌟"), 1e-10);
    }

    public function testMultibytePartialOverlap()
    {
        $result = Jaccard::index("café", "caffé");
        $this->assertEqualsWithDelta(1.0, $result, 1e-10);
    }

    public function testJapaneseCharacters()
    {
        $result = Jaccard::index("こんにちは", "こんにちわ");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testEmojiSupport()
    {
        $this->assertEqualsWithDelta(0.3333333333333333, Jaccard::index("🚀🌟", "🚀⭐"), 1e-10);
    }

    public function testCyrillicCharacters()
    {
        $result = Jaccard::index("собака", "собаки");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testHebrewCharacters()
    {
        $result = Jaccard::index("עברית", "עבדית");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testMixedAsciiMultibyte()
    {
        $result = Jaccard::index("hello café", "hello cafe");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testMultibyteWithEmptyString()
    {
        $this->assertEqualsWithDelta(0.0, Jaccard::index("café", ""), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaccard::index("", "🚀🌟"), 1e-10);
    }

    public function testSingleMultibyteCharacter()
    {
        $this->assertEqualsWithDelta(1.0, Jaccard::index("é", "é"), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaccard::index("é", "ö"), 1e-10);
        $this->assertEqualsWithDelta(1.0, Jaccard::index("🚀", "🚀"), 1e-10);
    }

    public function testMultibyteRepeatedCharacters()
    {
        $this->assertEqualsWithDelta(1.0, Jaccard::index("éééé", "é"), 1e-10);
    }

    public function testNormalizationCombiningMarks()
    {
        $this->assertEqualsWithDelta(0.0, Jaccard::index("é", "\u{0065}\u{0301}"), 1e-10);
    }

    public function testZWJFamilyEmojiSetOverlap()
    {
        $this->assertEqualsWithDelta(0.8, Jaccard::index("👨‍👩‍👧‍👦", "👨👩👧👦"), 1e-10);
    }

    public function testNFKCCompatibilityNormalization()
    {
        $this->assertEqualsWithDelta(0.0, Jaccard::index("①", "1"), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaccard::index("ﬀ", "ff"), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaccard::index("Å", "Å"), 1e-10);
    }

    public function testNFKDDecomposedNormalization()
    {
        $this->assertEqualsWithDelta(0.0, Jaccard::index("Å", "A\u{030A}"), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaccard::index("ñ", "n\u{0303}"), 1e-10);
    }

    public function testInvalidUtf8Input()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Input strings must be valid UTF-8.");
        $invalid = "\xFF\xFF";
        Jaccard::index($invalid, "test");
    }
}
