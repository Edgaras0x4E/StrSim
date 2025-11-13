<?php

declare(strict_types=1);

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Edgaras\StrSim\Cosine;

#[CoversClass(Cosine::class)]
class CosineTest extends TestCase
{
    public function testIdenticalStrings(): void
    {
        $this->assertEqualsWithDelta(1.0, Cosine::similarity("abc", "abc"), 1e-10);
    }

    public function testCompletelyDifferentStrings(): void
    {
        $this->assertEquals(0.0, Cosine::similarity("abc", "xyz"));
    }

    public function testPartiallySimilarStrings(): void
    {
        $a = "night";
        $b = "nacht";
        $result = Cosine::similarity($a, $b);
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testEmptyStrings(): void
    {
        $this->assertEquals(0.0, Cosine::similarity("", ""));
    }

    public function testOneEmptyString(): void
    {
        $this->assertEquals(0.0, Cosine::similarity("abc", ""));
        $this->assertEquals(0.0, Cosine::similarity("", "abc"));
    }


    public function testIdenticalVectors(): void
    {
        $this->assertEqualsWithDelta(1.0, Cosine::similarityFromVectors([1, 2, 3], [1, 2, 3]), 1e-10);
    }

    public function testCompletelyOppositeVectors(): void
    {
        $this->assertEqualsWithDelta(-1.0, Cosine::similarityFromVectors([1, 0], [-1, 0]), 1e-10);
    }

    public function testOrthogonalVectors(): void
    {
        $this->assertEqualsWithDelta(0.0, Cosine::similarityFromVectors([1, 0], [0, 1]), 1e-10);
    }

    public function testPartiallyAlignedVectors(): void
    {
        $a = [1, 1];
        $b = [1, 0];
        $similarity = Cosine::similarityFromVectors($a, $b);
        $this->assertGreaterThan(0, $similarity);
        $this->assertLessThan(1, $similarity);
    }

    public function testMismatchedVectorLengths(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Vectors must be the same length.");
        Cosine::similarityFromVectors([1, 2, 3], [1, 2]);
    }

    public function testZeroVectors(): void
    {
        $this->assertEquals(0.0, Cosine::similarityFromVectors([0, 0], [0, 0]));
    }

    public function testMultibyteIdenticalStrings(): void
    {
        $this->assertEqualsWithDelta(1.0, Cosine::similarity("café", "café"), 1e-10);
        $this->assertEqualsWithDelta(1.0, Cosine::similarity("🚀🌟", "🚀🌟"), 1e-10);
    }

    public function testMultibyteDifferentStrings(): void
    {
        $this->assertEquals(0.0, Cosine::similarity("café", "🚀🌟"));
    }

    public function testMultibytePartialSimilarity(): void
    {
        $result = Cosine::similarity("café", "caffé");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testJapaneseCharacters(): void
    {
        $result = Cosine::similarity("こんにちは", "こんにちわ");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testEmojiSimilarity(): void
    {
        $result = Cosine::similarity("🚀🌟", "🚀⭐");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testMixedAsciiMultibyte(): void
    {
        $result = Cosine::similarity("hello café", "hello cafe");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testCyrillicCharacters(): void
    {
        $result = Cosine::similarity("собака", "собаки");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testMultibyteWithEmptyString(): void
    {
        $this->assertEquals(0.0, Cosine::similarity("café", ""));
        $this->assertEquals(0.0, Cosine::similarity("", "🚀🌟"));
    }

    public function testSingleMultibyteCharacter(): void
    {
        $this->assertEqualsWithDelta(1.0, Cosine::similarity("é", "é"), 1e-10);
        $this->assertEquals(0.0, Cosine::similarity("é", "ö"));
        $this->assertEqualsWithDelta(1.0, Cosine::similarity("🚀", "🚀"), 1e-10);
    }

    public function testNormalizationCombiningMarks(): void
    {
        $this->assertEqualsWithDelta(0.0, Cosine::similarity("é", "\u{0065}\u{0301}"), 1e-10);
    }

    public function testZWJFamilyEmojiCosine(): void
    {
        $this->assertEqualsWithDelta(2 / sqrt(13), Cosine::similarity("👨‍👩‍👧‍👦", "👨👩👧👦"), 1e-9);
    }

    public function testNFKCCompatibilityNormalization(): void
    {
        $this->assertEquals(0.0, Cosine::similarity("①", "1"));
        $this->assertEquals(0.0, Cosine::similarity("ﬀ", "ff"));
        $this->assertEquals(0.0, Cosine::similarity("Å", "Å"));
    }

    public function testNFKDDecomposedNormalization(): void
    {
        $this->assertEquals(0.0, Cosine::similarity("Å", "A\u{030A}"));
        $this->assertEquals(0.0, Cosine::similarity("ñ", "n\u{0303}"));
    }

    public function testInvalidUtf8Input(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Input strings must be valid UTF-8.");
        $invalid = "\xFF\xFF";
        Cosine::similarity($invalid, "test");
    }
}
