<?php

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use Edgaras\StrSim\Jaro;
use Edgaras\StrSim\JaroWinkler;

class JaroWinklerTest extends TestCase
{ 
    public function testSimilarityIdenticalStrings()
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::similarity("martha", "martha"), 1e-10);
    }

    public function testSimilarityCompletelyDifferentStrings()
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::similarity("abc", "xyz"), 1e-10);
    }

    public function testSimilarityKnownPairMARTHAvsMARHTA()
    {
        $jaro = Jaro::similarity("martha", "marhta");  
        $prefix = 3;
        $expected = $jaro + $prefix * 0.1 * (1 - $jaro);
        $actual = JaroWinkler::similarity("martha", "marhta");
        $this->assertEqualsWithDelta($expected, $actual, 1e-6);
    }

    public function testSimilarityPrefixLimit()
    { 
        $a = "prefix_match_1";
        $b = "prefix_match_2";

        $jaro = Jaro::similarity($a, $b);
        $expected = $jaro + 4 * 0.1 * (1 - $jaro);  
        $actual = JaroWinkler::similarity($a, $b);
        $this->assertEqualsWithDelta($expected, $actual, 1e-6);
    }

    public function testSimilarityNoCommonPrefix()
    {
        $a = "xxxxx";
        $b = "yyyyy";
        $jaro = Jaro::similarity($a, $b);
        $expected = $jaro;
        $this->assertEqualsWithDelta($expected, JaroWinkler::similarity($a, $b), 1e-10);
    }

    public function testSimilarityEmptyStrings()
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::similarity("", ""), 1e-10);
    }

    public function testSimilarityOneEmptyString()
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::similarity("abc", ""), 1e-10);
        $this->assertEqualsWithDelta(0.0, JaroWinkler::similarity("", "xyz"), 1e-10);
    }

    public function testSimilaritySingleCharMatch()
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::similarity("a", "a"), 1e-10);
    }

    public function testSimilaritySingleCharMismatch()
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::similarity("a", "b"), 1e-10);
    }
 
    public function testDistanceIdenticalStrings()
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("martha", "martha"), 1e-10);
    }

    public function testDistanceCompletelyDifferentStrings()
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("abc", "xyz"), 1e-10);
    }

    public function testDistanceKnownPairMARTHAvsMARHTA()
    {
        $jaro = Jaro::similarity("martha", "marhta");  
        $prefix = 3;
        $expected = 1.0 - ($jaro + $prefix * 0.1 * (1 - $jaro));
        $actual = JaroWinkler::distance("martha", "marhta");
        $this->assertEqualsWithDelta($expected, $actual, 1e-6);
    }

    public function testDistancePrefixLimit()
    { 
        $a = "prefix_match_1";
        $b = "prefix_match_2";

        $jaro = Jaro::similarity($a, $b);
        $expected = 1.0 - ($jaro + 4 * 0.1 * (1 - $jaro));  
        $actual = JaroWinkler::distance($a, $b);
        $this->assertEqualsWithDelta($expected, $actual, 1e-6);
    }

    public function testDistanceNoCommonPrefix()
    {
        $a = "xxxxx";
        $b = "yyyyy";
        $jaro = Jaro::similarity($a, $b);
        $expected = 1.0 - $jaro;
        $this->assertEqualsWithDelta($expected, JaroWinkler::distance($a, $b), 1e-10);
    }

    public function testDistanceEmptyStrings()
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("", ""), 1e-10);
    }

    public function testDistanceOneEmptyString()
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("abc", ""), 1e-10);
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("", "xyz"), 1e-10);
    }

    public function testDistanceSingleCharMatch()
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("a", "a"), 1e-10);
    }

    public function testDistanceSingleCharMismatch()
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("a", "b"), 1e-10);
    }
 
    public function testSimilarityMultibyteIdenticalStrings()
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::similarity("café", "café"), 1e-10);
        $this->assertEqualsWithDelta(1.0, JaroWinkler::similarity("🚀🌟", "🚀🌟"), 1e-10);
    }

    public function testSimilarityMultibytePartialMatch()
    {
        $result = JaroWinkler::similarity("café", "caffé");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testSimilarityJapaneseCharacters()
    {
        $result = JaroWinkler::similarity("こんにちは", "こんにちわ");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testSimilarityEmojiSupport()
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::similarity("🚀", "🚀"), 1e-10);
        $this->assertEqualsWithDelta(Jaro::similarity("🚀🌟", "🚀⭐") + 0.1 * (1 - Jaro::similarity("🚀🌟", "🚀⭐")), JaroWinkler::similarity("🚀🌟", "🚀⭐"), 1e-6);
    }

    public function testSimilarityCyrillicCharacters()
    {
        $result = JaroWinkler::similarity("собака", "собаки");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testSimilarityHebrewCharacters()
    {
        $result = JaroWinkler::similarity("עברית", "עבדית");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testSimilarityMixedAsciiMultibyte()
    {
        $result = JaroWinkler::similarity("hello café", "hello cafe");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testSimilarityMultibyteCompletelyDifferent()
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::similarity("café", "🚀🌟"), 1e-10);
    }

    public function testSimilarityMultibyteWithEmptyString()
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::similarity("café", ""), 1e-10);
        $this->assertEqualsWithDelta(0.0, JaroWinkler::similarity("🚀🌟", ""), 1e-10);
    }

    public function testSimilaritySingleMultibyteCharacter()
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::similarity("é", "é"), 1e-10);
        $this->assertEqualsWithDelta(0.0, JaroWinkler::similarity("é", "ö"), 1e-10);
        $this->assertEqualsWithDelta(1.0, JaroWinkler::similarity("🚀", "🚀"), 1e-10);
    }
 
    public function testDistanceMultibyteIdenticalStrings()
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("café", "café"), 1e-10);
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("🚀🌟", "🚀🌟"), 1e-10);
    }

    public function testDistanceMultibytePartialMatch()
    {
        $result = JaroWinkler::distance("café", "caffé");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testDistanceJapaneseCharacters()
    {
        $result = JaroWinkler::distance("こんにちは", "こんにちわ");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testDistanceEmojiSupport()
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("🚀", "🚀"), 1e-10);
        $jaroSim = Jaro::similarity("🚀🌟", "🚀⭐");
        $expectedSim = $jaroSim + 0.1 * (1 - $jaroSim);
        $this->assertEqualsWithDelta(1.0 - $expectedSim, JaroWinkler::distance("🚀🌟", "🚀⭐"), 1e-6);
    }

    public function testDistanceCyrillicCharacters()
    {
        $result = JaroWinkler::distance("собака", "собаки");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testDistanceHebrewCharacters()
    {
        $result = JaroWinkler::distance("עברית", "עבדית");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testDistanceMixedAsciiMultibyte()
    {
        $result = JaroWinkler::distance("hello café", "hello cafe");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testDistanceMultibyteCompletelyDifferent()
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("café", "🚀🌟"), 1e-10);
    }

    public function testDistanceMultibyteWithEmptyString()
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("café", ""), 1e-10);
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("🚀🌟", ""), 1e-10);
    }

    public function testDistanceSingleMultibyteCharacter()
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("é", "é"), 1e-10);
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("é", "ö"), 1e-10);
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("🚀", "🚀"), 1e-10);
    }

    public function testSimilarityMultibytePrefix()
    {
        $withoutPrefix = JaroWinkler::similarity("ébcdef", "éxydef");
        $withPrefix = JaroWinkler::similarity("éébcdef", "ééxydef");
        $this->assertGreaterThan($withoutPrefix, $withPrefix);
    }

    public function testSimilarityMultibytePrefixScale()
    {
        $defaultScale = JaroWinkler::similarity("café", "cafe");
        $customScale = JaroWinkler::similarity("café", "cafe", 0.2);
        $this->assertNotEquals($defaultScale, $customScale);
    }

    public function testSimilarityPrefixScaleValidation()
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

    public function testDistanceMultibytePrefix()
    {
        $withoutPrefix = JaroWinkler::distance("ébcdef", "éxydef");
        $withPrefix = JaroWinkler::distance("éébcdef", "ééxydef");
        $this->assertLessThan($withoutPrefix, $withPrefix);  
    }

    public function testDistanceMultibytePrefixScale()
    {
        $defaultScale = JaroWinkler::distance("café", "cafe");
        $customScale = JaroWinkler::distance("café", "cafe", 0.2);
        $this->assertNotEquals($defaultScale, $customScale);
    }

    public function testDistancePrefixScaleValidation()
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

    public function testDistanceWindowBoundaryCases()
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("a", "b"), 1e-10);
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("a", "a"), 1e-10);
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("ab", "cd"), 1e-10);
        $result = JaroWinkler::distance("ab", "ba");
        $this->assertEqualsWithDelta(1.0, $result, 1e-10);
    }

    public function testSimilarityReferenceValues()
    {
        $this->assertEqualsWithDelta(0.9611111111, JaroWinkler::similarity("MARTHA", "MARHTA"), 1e-9);
        $this->assertEqualsWithDelta(0.84, JaroWinkler::similarity("DWAYNE", "DUANE"), 1e-9);
        $this->assertEqualsWithDelta(0.8133333333, JaroWinkler::similarity("DIXON", "DICKSONX"), 1e-9);
    }

    public function testSimilarityMaximumPrefixScale()
    {
        $this->assertSame(1.0, JaroWinkler::similarity("abcdwxyz", "abcdqrst", 0.25));
    }

    public function testInvalidUtf8InputDistance()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Input strings must be valid UTF-8.");
        $invalid = "\xFF\xFF";
        JaroWinkler::distance($invalid, "test");
    }

    public function testInvalidUtf8InputSimilarity()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Input strings must be valid UTF-8.");
        $invalid = "\xFF\xFF";
        JaroWinkler::similarity($invalid, "test");
    }
}