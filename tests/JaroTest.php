<?php

declare(strict_types=1);

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Edgaras\StrSim\Jaro;

#[CoversClass(Jaro::class)]
class JaroTest extends TestCase
{
    public function testSimilarityIdenticalStrings(): void
    {
        $this->assertEqualsWithDelta(1.0, Jaro::similarity("martha", "martha"), 1e-10);
    }

    public function testSimilarityCompletelyDifferentStrings(): void
    {
        $this->assertEqualsWithDelta(0.0, Jaro::similarity("abc", "xyz"), 1e-10);
    }

    public function testSimilarityPartialMatch(): void
    {
        $expected = 0.9444444444;
        $actual = Jaro::similarity("martha", "marhta");
        $this->assertEqualsWithDelta($expected, $actual, 1e-6);
    }

    public function testSimilarityCaseWithSomeOverlap(): void
    {
        $expected = 0.8222222222;
        $actual = Jaro::similarity("dwayne", "duane");
        $this->assertEqualsWithDelta($expected, $actual, 1e-6);
    }

    public function testSimilarityEmptyStrings(): void
    {
        $this->assertEqualsWithDelta(1.0, Jaro::similarity("", ""), 1e-10);
    }

    public function testSimilarityOneEmptyString(): void
    {
        $this->assertEqualsWithDelta(0.0, Jaro::similarity("abc", ""), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaro::similarity("", "abc"), 1e-10);
    }

    public function testSimilaritySingleCharacterMismatch(): void
    {
        $this->assertEqualsWithDelta(0.0, Jaro::similarity("a", "b"), 1e-10);
    }

    public function testSimilaritySingleCharacterMatch(): void
    {
        $this->assertEqualsWithDelta(1.0, Jaro::similarity("a", "a"), 1e-10);
    }

    public function testDistanceIdenticalStrings(): void
    {
        $this->assertEqualsWithDelta(0.0, Jaro::distance("martha", "martha"), 1e-10);
    }

    public function testDistanceCompletelyDifferentStrings(): void
    {
        $this->assertEqualsWithDelta(1.0, Jaro::distance("abc", "xyz"), 1e-10);
    }

    public function testDistancePartialMatch(): void
    {
        $expected = 1.0 - 0.9444444444;
        $actual = Jaro::distance("martha", "marhta");
        $this->assertEqualsWithDelta($expected, $actual, 1e-6);
    }

    public function testDistanceCaseWithSomeOverlap(): void
    {
        $expected = 1.0 - 0.8222222222;
        $actual = Jaro::distance("dwayne", "duane");
        $this->assertEqualsWithDelta($expected, $actual, 1e-6);
    }

    public function testDistanceEmptyStrings(): void
    {
        $this->assertEqualsWithDelta(0.0, Jaro::distance("", ""), 1e-10);
    }

    public function testDistanceOneEmptyString(): void
    {
        $this->assertEqualsWithDelta(1.0, Jaro::distance("abc", ""), 1e-10);
        $this->assertEqualsWithDelta(1.0, Jaro::distance("", "abc"), 1e-10);
    }

    public function testDistanceSingleCharacterMismatch(): void
    {
        $this->assertEqualsWithDelta(1.0, Jaro::distance("a", "b"), 1e-10);
    }

    public function testDistanceSingleCharacterMatch(): void
    {
        $this->assertEqualsWithDelta(0.0, Jaro::distance("a", "a"), 1e-10);
    }

    public function testSimilarityMultibyteIdenticalStrings(): void
    {
        $this->assertEqualsWithDelta(1.0, Jaro::similarity("café", "café"), 1e-10);
        $this->assertEqualsWithDelta(1.0, Jaro::similarity("🚀🌟", "🚀🌟"), 1e-10);
    }

    public function testSimilarityMultibytePartialMatch(): void
    {
        $result = Jaro::similarity("café", "caffé");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testSimilarityJapaneseCharacters(): void
    {
        $result = Jaro::similarity("こんにちは", "こんにちわ");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testSimilarityEmojiSupport(): void
    {
        $this->assertEqualsWithDelta(1.0, Jaro::similarity("🚀", "🚀"), 1e-10);
        $result = Jaro::similarity("🚀🌟", "🚀⭐");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testSimilarityCyrillicCharacters(): void
    {
        $result = Jaro::similarity("собака", "собаки");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testSimilarityHebrewCharacters(): void
    {
        $result = Jaro::similarity("עברית", "עבדית");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testSimilarityMixedAsciiMultibyte(): void
    {
        $result = Jaro::similarity("hello café", "hello cafe");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testSimilarityMultibyteCompletelyDifferent(): void
    {
        $this->assertEqualsWithDelta(0.0, Jaro::similarity("café", "🚀🌟"), 1e-10);
    }

    public function testSimilarityMultibyteWithEmptyString(): void
    {
        $this->assertEqualsWithDelta(0.0, Jaro::similarity("café", ""), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaro::similarity("🚀🌟", ""), 1e-10);
    }

    public function testSimilaritySingleMultibyteCharacter(): void
    {
        $this->assertEqualsWithDelta(1.0, Jaro::similarity("é", "é"), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaro::similarity("é", "ö"), 1e-10);
        $this->assertEqualsWithDelta(1.0, Jaro::similarity("🚀", "🚀"), 1e-10);
    }

    public function testDistanceMultibyteIdenticalStrings(): void
    {
        $this->assertEqualsWithDelta(0.0, Jaro::distance("café", "café"), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaro::distance("🚀🌟", "🚀🌟"), 1e-10);
    }

    public function testDistanceMultibytePartialMatch(): void
    {
        $result = Jaro::distance("café", "caffé");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testDistanceJapaneseCharacters(): void
    {
        $result = Jaro::distance("こんにちは", "こんにちわ");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testDistanceEmojiSupport(): void
    {
        $this->assertEqualsWithDelta(0.0, Jaro::distance("🚀", "🚀"), 1e-10);
        $result = Jaro::distance("🚀🌟", "🚀⭐");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testDistanceCyrillicCharacters(): void
    {
        $result = Jaro::distance("собака", "собаки");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testDistanceHebrewCharacters(): void
    {
        $result = Jaro::distance("עברית", "עבדית");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testDistanceMixedAsciiMultibyte(): void
    {
        $result = Jaro::distance("hello café", "hello cafe");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testDistanceMultibyteCompletelyDifferent(): void
    {
        $this->assertEqualsWithDelta(1.0, Jaro::distance("café", "🚀🌟"), 1e-10);
    }

    public function testDistanceMultibyteWithEmptyString(): void
    {
        $this->assertEqualsWithDelta(1.0, Jaro::distance("café", ""), 1e-10);
        $this->assertEqualsWithDelta(1.0, Jaro::distance("🚀🌟", ""), 1e-10);
    }

    public function testDistanceSingleMultibyteCharacter(): void
    {
        $this->assertEqualsWithDelta(0.0, Jaro::distance("é", "é"), 1e-10);
        $this->assertEqualsWithDelta(1.0, Jaro::distance("é", "ö"), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaro::distance("🚀", "🚀"), 1e-10);
    }

    public function testDistanceWindowBoundaryCases(): void
    {
        $this->assertEqualsWithDelta(1.0, Jaro::distance("a", "b"), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaro::distance("a", "a"), 1e-10);
        $this->assertEqualsWithDelta(1.0, Jaro::distance("ab", "cd"), 1e-10);
        $this->assertEqualsWithDelta(1.0, Jaro::distance("ab", "ba"), 1e-10);
        $this->assertEqualsWithDelta(1.0, Jaro::distance("abcd", "efgh"), 1e-10);
    }

    public function testDistanceExactJaroValues(): void
    {
        $this->assertEqualsWithDelta(1.0, Jaro::distance("abc", "xyz"), 1e-10);
        $this->assertEqualsWithDelta(1.0, Jaro::distance("a", "b"), 1e-10);
        $this->assertEqualsWithDelta(1.0, Jaro::distance("ab", "cd"), 1e-10);
        $result = Jaro::distance("abcdef", "fedcba");
        $this->assertGreaterThan(0.0, $result);
        $this->assertLessThan(1.0, $result);
    }

    public function testInvalidUtf8InputDistance(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Input strings must be valid UTF-8.");
        $invalid = "\xFF\xFF";
        Jaro::distance($invalid, "test");
    }

    public function testInvalidUtf8InputSimilarity(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Input strings must be valid UTF-8.");
        $invalid = "\xFF\xFF";
        Jaro::similarity($invalid, "test");
    }
}
