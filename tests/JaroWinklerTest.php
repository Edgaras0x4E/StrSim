<?php

declare(strict_types=1);

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Edgaras\StrSim\Jaro;
use Edgaras\StrSim\JaroWinkler;

#[CoversClass(JaroWinkler::class)]
#[UsesClass(Jaro::class)]
class JaroWinklerTest extends TestCase
{
    public function testSimilarityIdenticalStrings(): void
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::similarity("martha", "martha"), 1e-10);
    }

    public function testSimilarityCompletelyDifferentStrings(): void
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::similarity("abc", "xyz"), 1e-10);
    }

    public function testSimilarityKnownPairMARTHAvsMARHTA(): void
    {
        $jaro = Jaro::similarity("martha", "marhta");
        $prefix = 3;
        $expected = $jaro + $prefix * 0.1 * (1 - $jaro);
        $actual = JaroWinkler::similarity("martha", "marhta");
        $this->assertEqualsWithDelta($expected, $actual, 1e-6);
    }

    public function testSimilarityPrefixLimit(): void
    {
        $a = "prefix_match_1";
        $b = "prefix_match_2";

        $jaro = Jaro::similarity($a, $b);
        $expected = $jaro + 4 * 0.1 * (1 - $jaro);
        $actual = JaroWinkler::similarity($a, $b);
        $this->assertEqualsWithDelta($expected, $actual, 1e-6);
    }

    public function testSimilarityNoCommonPrefix(): void
    {
        $a = "xxxxx";
        $b = "yyyyy";
        $jaro = Jaro::similarity($a, $b);
        $expected = $jaro;
        $this->assertEqualsWithDelta($expected, JaroWinkler::similarity($a, $b), 1e-10);
    }

    public function testSimilarityEmptyStrings(): void
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::similarity("", ""), 1e-10);
    }

    public function testSimilarityOneEmptyString(): void
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::similarity("abc", ""), 1e-10);
        $this->assertEqualsWithDelta(0.0, JaroWinkler::similarity("", "xyz"), 1e-10);
    }

    public function testSimilaritySingleCharMatch(): void
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::similarity("a", "a"), 1e-10);
    }

    public function testSimilaritySingleCharMismatch(): void
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::similarity("a", "b"), 1e-10);
    }

    public function testDistanceIdenticalStrings(): void
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("martha", "martha"), 1e-10);
    }

    public function testDistanceCompletelyDifferentStrings(): void
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("abc", "xyz"), 1e-10);
    }

    public function testDistanceKnownPairMARTHAvsMARHTA(): void
    {
        $jaro = Jaro::similarity("martha", "marhta");
        $prefix = 3;
        $expected = 1.0 - ($jaro + $prefix * 0.1 * (1 - $jaro));
        $actual = JaroWinkler::distance("martha", "marhta");
        $this->assertEqualsWithDelta($expected, $actual, 1e-6);
    }

    public function testDistancePrefixLimit(): void
    {
        $a = "prefix_match_1";
        $b = "prefix_match_2";

        $jaro = Jaro::similarity($a, $b);
        $expected = 1.0 - ($jaro + 4 * 0.1 * (1 - $jaro));
        $actual = JaroWinkler::distance($a, $b);
        $this->assertEqualsWithDelta($expected, $actual, 1e-6);
    }

    public function testDistanceNoCommonPrefix(): void
    {
        $a = "xxxxx";
        $b = "yyyyy";
        $jaro = Jaro::similarity($a, $b);
        $expected = 1.0 - $jaro;
        $this->assertEqualsWithDelta($expected, JaroWinkler::distance($a, $b), 1e-10);
    }

    public function testDistanceEmptyStrings(): void
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("", ""), 1e-10);
    }

    public function testDistanceOneEmptyString(): void
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("abc", ""), 1e-10);
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("", "xyz"), 1e-10);
    }

    public function testDistanceSingleCharMatch(): void
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("a", "a"), 1e-10);
    }

    public function testDistanceSingleCharMismatch(): void
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("a", "b"), 1e-10);
    }

    public function testSimilarityMultibyteIdenticalStrings(): void
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::similarity("café", "café"), 1e-10);
        $this->assertEqualsWithDelta(1.0, JaroWinkler::similarity("🚀🌟", "🚀🌟"), 1e-10);
    }

    public function testSimilarityMultibytePartialMatch(): void
    {
        $result = JaroWinkler::similarity("café", "caffé");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testSimilarityJapaneseCharacters(): void
    {
        $result = JaroWinkler::similarity("こんにちは", "こんにちわ");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testSimilarityEmojiSupport(): void
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::similarity("🚀", "🚀"), 1e-10);
        $this->assertEqualsWithDelta(Jaro::similarity("🚀🌟", "🚀⭐") + 0.1 * (1 - Jaro::similarity("🚀🌟", "🚀⭐")), JaroWinkler::similarity("🚀🌟", "🚀⭐"), 1e-6);
    }

    public function testSimilarityCyrillicCharacters(): void
    {
        $result = JaroWinkler::similarity("собака", "собаки");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testSimilarityHebrewCharacters(): void
    {
        $result = JaroWinkler::similarity("עברית", "עבדית");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testSimilarityMixedAsciiMultibyte(): void
    {
        $result = JaroWinkler::similarity("hello café", "hello cafe");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testSimilarityMultibyteCompletelyDifferent(): void
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::similarity("café", "🚀🌟"), 1e-10);
    }

    public function testSimilarityMultibyteWithEmptyString(): void
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::similarity("café", ""), 1e-10);
        $this->assertEqualsWithDelta(0.0, JaroWinkler::similarity("🚀🌟", ""), 1e-10);
    }

    public function testSimilaritySingleMultibyteCharacter(): void
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::similarity("é", "é"), 1e-10);
        $this->assertEqualsWithDelta(0.0, JaroWinkler::similarity("é", "ö"), 1e-10);
        $this->assertEqualsWithDelta(1.0, JaroWinkler::similarity("🚀", "🚀"), 1e-10);
    }

    public function testDistanceMultibyteIdenticalStrings(): void
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("café", "café"), 1e-10);
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("🚀🌟", "🚀🌟"), 1e-10);
    }

    public function testDistanceMultibytePartialMatch(): void
    {
        $result = JaroWinkler::distance("café", "caffé");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testDistanceJapaneseCharacters(): void
    {
        $result = JaroWinkler::distance("こんにちは", "こんにちわ");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testDistanceEmojiSupport(): void
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("🚀", "🚀"), 1e-10);
        $jaroSim = Jaro::similarity("🚀🌟", "🚀⭐");
        $expectedSim = $jaroSim + 0.1 * (1 - $jaroSim);
        $this->assertEqualsWithDelta(1.0 - $expectedSim, JaroWinkler::distance("🚀🌟", "🚀⭐"), 1e-6);
    }

    public function testDistanceCyrillicCharacters(): void
    {
        $result = JaroWinkler::distance("собака", "собаки");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testDistanceHebrewCharacters(): void
    {
        $result = JaroWinkler::distance("עברית", "עבדית");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testDistanceMixedAsciiMultibyte(): void
    {
        $result = JaroWinkler::distance("hello café", "hello cafe");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testDistanceMultibyteCompletelyDifferent(): void
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("café", "🚀🌟"), 1e-10);
    }

    public function testDistanceMultibyteWithEmptyString(): void
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("café", ""), 1e-10);
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("🚀🌟", ""), 1e-10);
    }

    public function testDistanceSingleMultibyteCharacter(): void
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("é", "é"), 1e-10);
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("é", "ö"), 1e-10);
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("🚀", "🚀"), 1e-10);
    }

    public function testSimilarityMultibytePrefix(): void
    {
        $withoutPrefix = JaroWinkler::similarity("ébcdef", "éxydef");
        $withPrefix = JaroWinkler::similarity("éébcdef", "ééxydef");
        $this->assertGreaterThan($withoutPrefix, $withPrefix);
    }

    public function testSimilarityMultibytePrefixScale(): void
    {
        $defaultScale = JaroWinkler::similarity("café", "cafe");
        $customScale = JaroWinkler::similarity("café", "cafe", 0.2);
        $this->assertNotEquals($defaultScale, $customScale);
    }

    public function testSimilarityPrefixScaleValidation(): void
    {
        $base = Jaro::similarity("martha", "marhta");
        $scale1 = JaroWinkler::similarity("martha", "marhta", 0.1);
        $scale2 = JaroWinkler::similarity("martha", "marhta", 0.2);
        $expected1 = $base + 3 * 0.1 * (1 - $base);
        $expected2 = $base + 3 * 0.2 * (1 - $base);
        $this->assertEqualsWithDelta($expected1, $scale1, 1e-6);
        $this->assertEqualsWithDelta($expected2, $scale2, 1e-6);
        $this->assertGreaterThan($scale1, $scale2);
    }

    public function testDistanceMultibytePrefix(): void
    {
        $withoutPrefix = JaroWinkler::distance("ébcdef", "éxydef");
        $withPrefix = JaroWinkler::distance("éébcdef", "ééxydef");
        $this->assertLessThan($withoutPrefix, $withPrefix);
    }

    public function testDistanceMultibytePrefixScale(): void
    {
        $defaultScale = JaroWinkler::distance("café", "cafe");
        $customScale = JaroWinkler::distance("café", "cafe", 0.2);
        $this->assertNotEquals($defaultScale, $customScale);
    }

    public function testDistancePrefixScaleValidation(): void
    {
        $base = Jaro::similarity("martha", "marhta");
        $scale1 = JaroWinkler::distance("martha", "marhta", 0.1);
        $scale2 = JaroWinkler::distance("martha", "marhta", 0.2);
        $expected1 = 1.0 - ($base + 3 * 0.1 * (1 - $base));
        $expected2 = 1.0 - ($base + 3 * 0.2 * (1 - $base));
        $this->assertEqualsWithDelta($expected1, $scale1, 1e-6);
        $this->assertEqualsWithDelta($expected2, $scale2, 1e-6);
        $this->assertLessThan($scale1, $scale2);
    }

    public function testDistanceWindowBoundaryCases(): void
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("a", "b"), 1e-10);
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("a", "a"), 1e-10);
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("ab", "cd"), 1e-10);
        $result = JaroWinkler::distance("ab", "ba");
        $this->assertEqualsWithDelta(1.0, $result, 1e-10);
    }

    public function testInvalidUtf8InputDistance(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Input strings must be valid UTF-8.");
        $invalid = "\xFF\xFF";
        JaroWinkler::distance($invalid, "test");
    }

    public function testInvalidUtf8InputSimilarity(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Input strings must be valid UTF-8.");
        $invalid = "\xFF\xFF";
        JaroWinkler::similarity($invalid, "test");
    }
}
