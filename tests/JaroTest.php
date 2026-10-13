<?php

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use Edgaras\StrSim\Jaro;

class JaroTest extends TestCase
{
   
    public function testSimilarityIdenticalStrings()
    {
        $this->assertEqualsWithDelta(1.0, Jaro::similarity("martha", "martha"), 1e-10);
    }

    public function testSimilarityCompletelyDifferentStrings()
    {
        $this->assertEqualsWithDelta(0.0, Jaro::similarity("abc", "xyz"), 1e-10);
    }

    public function testSimilarityPartialMatch()
    {
        $expected = 0.9444444444;
        $actual = Jaro::similarity("martha", "marhta");
        $this->assertEqualsWithDelta($expected, $actual, 1e-6);
    }

    public function testSimilarityCaseWithSomeOverlap()
    {
        $expected = 0.8222222222;
        $actual = Jaro::similarity("dwayne", "duane");
        $this->assertEqualsWithDelta($expected, $actual, 1e-6);
    }

    public function testSimilarityEmptyStrings()
    {
        $this->assertEqualsWithDelta(1.0, Jaro::similarity("", ""), 1e-10);
    }

    public function testSimilarityOneEmptyString()
    {
        $this->assertEqualsWithDelta(0.0, Jaro::similarity("abc", ""), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaro::similarity("", "abc"), 1e-10);
    }

    public function testSimilaritySingleCharacterMismatch()
    {
        $this->assertEqualsWithDelta(0.0, Jaro::similarity("a", "b"), 1e-10);
    }

    public function testSimilaritySingleCharacterMatch()
    {
        $this->assertEqualsWithDelta(1.0, Jaro::similarity("a", "a"), 1e-10);
    }
 
    public function testDistanceIdenticalStrings()
    {
        $this->assertEqualsWithDelta(0.0, Jaro::distance("martha", "martha"), 1e-10);
    }

    public function testDistanceCompletelyDifferentStrings()
    {
        $this->assertEqualsWithDelta(1.0, Jaro::distance("abc", "xyz"), 1e-10);
    }

    public function testDistancePartialMatch()
    {
        $expected = 1.0 - 0.9444444444;
        $actual = Jaro::distance("martha", "marhta");
        $this->assertEqualsWithDelta($expected, $actual, 1e-6);
    }

    public function testDistanceCaseWithSomeOverlap()
    {
        $expected = 1.0 - 0.8222222222;
        $actual = Jaro::distance("dwayne", "duane");
        $this->assertEqualsWithDelta($expected, $actual, 1e-6);
    }

    public function testDistanceEmptyStrings()
    {
        $this->assertEqualsWithDelta(0.0, Jaro::distance("", ""), 1e-10);
    }

    public function testDistanceOneEmptyString()
    {
        $this->assertEqualsWithDelta(1.0, Jaro::distance("abc", ""), 1e-10);
        $this->assertEqualsWithDelta(1.0, Jaro::distance("", "abc"), 1e-10);
    }

    public function testDistanceSingleCharacterMismatch()
    {
        $this->assertEqualsWithDelta(1.0, Jaro::distance("a", "b"), 1e-10);
    }

    public function testDistanceSingleCharacterMatch()
    {
        $this->assertEqualsWithDelta(0.0, Jaro::distance("a", "a"), 1e-10);
    }
 
    public function testSimilarityMultibyteIdenticalStrings()
    {
        $this->assertEqualsWithDelta(1.0, Jaro::similarity("café", "café"), 1e-10);
        $this->assertEqualsWithDelta(1.0, Jaro::similarity("🚀🌟", "🚀🌟"), 1e-10);
    }

    public function testSimilarityMultibytePartialMatch()
    {
        $result = Jaro::similarity("café", "caffé");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testSimilarityJapaneseCharacters()
    {
        $result = Jaro::similarity("こんにちは", "こんにちわ");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testSimilarityEmojiSupport()
    {
        $this->assertEqualsWithDelta(1.0, Jaro::similarity("🚀", "🚀"), 1e-10);
        $result = Jaro::similarity("🚀🌟", "🚀⭐");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testSimilarityCyrillicCharacters()
    {
        $result = Jaro::similarity("собака", "собаки");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testSimilarityHebrewCharacters()
    {
        $result = Jaro::similarity("עברית", "עבדית");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testSimilarityMixedAsciiMultibyte()
    {
        $result = Jaro::similarity("hello café", "hello cafe");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testSimilarityMultibyteCompletelyDifferent()
    {
        $this->assertEqualsWithDelta(0.0, Jaro::similarity("café", "🚀🌟"), 1e-10);
    }

    public function testSimilarityMultibyteWithEmptyString()
    {
        $this->assertEqualsWithDelta(0.0, Jaro::similarity("café", ""), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaro::similarity("🚀🌟", ""), 1e-10);
    }

    public function testSimilaritySingleMultibyteCharacter()
    {
        $this->assertEqualsWithDelta(1.0, Jaro::similarity("é", "é"), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaro::similarity("é", "ö"), 1e-10);
        $this->assertEqualsWithDelta(1.0, Jaro::similarity("🚀", "🚀"), 1e-10);
    }
 
    public function testDistanceMultibyteIdenticalStrings()
    {
        $this->assertEqualsWithDelta(0.0, Jaro::distance("café", "café"), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaro::distance("🚀🌟", "🚀🌟"), 1e-10);
    }

    public function testDistanceMultibytePartialMatch()
    {
        $result = Jaro::distance("café", "caffé");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testDistanceJapaneseCharacters()
    {
        $result = Jaro::distance("こんにちは", "こんにちわ");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testDistanceEmojiSupport()
    {
        $this->assertEqualsWithDelta(0.0, Jaro::distance("🚀", "🚀"), 1e-10);
        $result = Jaro::distance("🚀🌟", "🚀⭐");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testDistanceCyrillicCharacters()
    {
        $result = Jaro::distance("собака", "собаки");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testDistanceHebrewCharacters()
    {
        $result = Jaro::distance("עברית", "עבדית");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testDistanceMixedAsciiMultibyte()
    {
        $result = Jaro::distance("hello café", "hello cafe");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testDistanceMultibyteCompletelyDifferent()
    {
        $this->assertEqualsWithDelta(1.0, Jaro::distance("café", "🚀🌟"), 1e-10);
    }

    public function testDistanceMultibyteWithEmptyString()
    {
        $this->assertEqualsWithDelta(1.0, Jaro::distance("café", ""), 1e-10);
        $this->assertEqualsWithDelta(1.0, Jaro::distance("🚀🌟", ""), 1e-10);
    }

    public function testDistanceSingleMultibyteCharacter()
    {
        $this->assertEqualsWithDelta(0.0, Jaro::distance("é", "é"), 1e-10);
        $this->assertEqualsWithDelta(1.0, Jaro::distance("é", "ö"), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaro::distance("🚀", "🚀"), 1e-10);
    }

    public function testDistanceWindowBoundaryCases()
    {
        $this->assertEqualsWithDelta(1.0, Jaro::distance("a", "b"), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaro::distance("a", "a"), 1e-10);
        $this->assertEqualsWithDelta(1.0, Jaro::distance("ab", "cd"), 1e-10);
        $this->assertEqualsWithDelta(1.0, Jaro::distance("ab", "ba"), 1e-10);
        $this->assertEqualsWithDelta(1.0, Jaro::distance("abcd", "efgh"), 1e-10);
    }

    public function testDistanceExactJaroValues()
    {
        $this->assertEqualsWithDelta(1.0, Jaro::distance("abc", "xyz"), 1e-10);
        $this->assertEqualsWithDelta(1.0, Jaro::distance("a", "b"), 1e-10);
        $this->assertEqualsWithDelta(1.0, Jaro::distance("ab", "cd"), 1e-10);
        $result = Jaro::distance("abcdef", "fedcba");
        $this->assertGreaterThan(0.0, $result);
        $this->assertLessThan(1.0, $result);
    }

    public function testSimilarityReferenceValues()
    {
        $this->assertEqualsWithDelta(0.9444444444, Jaro::similarity("MARTHA", "MARHTA"), 1e-9);
        $this->assertEqualsWithDelta(0.8222222222, Jaro::similarity("DWAYNE", "DUANE"), 1e-9);
        $this->assertEqualsWithDelta(0.7666666667, Jaro::similarity("DIXON", "DICKSONX"), 1e-9);
        $this->assertEqualsWithDelta(0.8962962963, Jaro::similarity("JELLYFISH", "SMELLYFISH"), 1e-9);
        $this->assertEqualsWithDelta(0.7333333333, Jaro::similarity("CRATE", "TRACE"), 1e-9);
    }

    public function testInvalidUtf8InputDistance()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Input strings must be valid UTF-8.");
        $invalid = "\xFF\xFF";
        Jaro::distance($invalid, "test");
    }

    public function testInvalidUtf8InputSimilarity()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Input strings must be valid UTF-8.");
        $invalid = "\xFF\xFF";
        Jaro::similarity($invalid, "test");
    }
}