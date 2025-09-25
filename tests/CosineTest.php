<?php

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use Edgaras\StrSim\Cosine;

class CosineTest extends TestCase
{
    public function testIdenticalStrings()
    {
        $this->assertEqualsWithDelta(1.0, Cosine::similarity("abc", "abc"), 1e-10);
    }

    public function testCompletelyDifferentStrings()
    {
        $this->assertEquals(0.0, Cosine::similarity("abc", "xyz"));
    }

    public function testPartiallySimilarStrings()
    {
        $a = "night";
        $b = "nacht";
        $result = Cosine::similarity($a, $b);
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testEmptyStrings()
    {
        $this->assertEquals(0.0, Cosine::similarity("", ""));
    }

    public function testOneEmptyString()
    {
        $this->assertEquals(0.0, Cosine::similarity("abc", ""));
        $this->assertEquals(0.0, Cosine::similarity("", "abc"));
    }
    

    public function testIdenticalVectors()
    {
        $this->assertEqualsWithDelta(1.0, Cosine::similarityFromVectors([1, 2, 3], [1, 2, 3]), 1e-10);
    }

    public function testCompletelyOppositeVectors()
    {
        $this->assertEqualsWithDelta(-1.0, Cosine::similarityFromVectors([1, 0], [-1, 0]), 1e-10);
    }

    public function testOrthogonalVectors()
    {
        $this->assertEqualsWithDelta(0.0, Cosine::similarityFromVectors([1, 0], [0, 1]), 1e-10);
    }

    public function testPartiallyAlignedVectors()
    {
        $a = [1, 1];
        $b = [1, 0];
        $similarity = Cosine::similarityFromVectors($a, $b);
        $this->assertGreaterThan(0, $similarity);
        $this->assertLessThan(1, $similarity);
    }

    public function testMismatchedVectorLengths()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Vectors must be the same length.");
        Cosine::similarityFromVectors([1, 2, 3], [1, 2]);
    }

    public function testZeroVectors()
    {
        $this->assertEquals(0.0, Cosine::similarityFromVectors([0, 0], [0, 0]));
    }

    public function testMultibyteIdenticalStrings()
    {
        $this->assertEqualsWithDelta(1.0, Cosine::similarity("café", "café"), 1e-10);
        $this->assertEqualsWithDelta(1.0, Cosine::similarity("🚀🌟", "🚀🌟"), 1e-10);
    }

    public function testMultibyteDifferentStrings()
    {
        $this->assertEquals(0.0, Cosine::similarity("café", "🚀🌟"));
    }

    public function testMultibytePartialSimilarity()
    {
        $result = Cosine::similarity("café", "caffé");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testJapaneseCharacters()
    {
        $result = Cosine::similarity("こんにちは", "こんにちわ");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testEmojiSimilarity()
    {
        $result = Cosine::similarity("🚀🌟", "🚀⭐");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testMixedAsciiMultibyte()
    {
        $result = Cosine::similarity("hello café", "hello cafe");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testCyrillicCharacters()
    {
        $result = Cosine::similarity("собака", "собаки");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testMultibyteWithEmptyString()
    {
        $this->assertEquals(0.0, Cosine::similarity("café", ""));
        $this->assertEquals(0.0, Cosine::similarity("", "🚀🌟"));
    }

    public function testSingleMultibyteCharacter()
    {
        $this->assertEqualsWithDelta(1.0, Cosine::similarity("é", "é"), 1e-10);
        $this->assertEquals(0.0, Cosine::similarity("é", "ö"));
        $this->assertEqualsWithDelta(1.0, Cosine::similarity("🚀", "🚀"), 1e-10);
    }

    public function testNormalizationCombiningMarks()
    {
        $this->assertEqualsWithDelta(0.0, Cosine::similarity("é", "\u{0065}\u{0301}"), 1e-10);
    }

    public function testZWJFamilyEmojiCosine()
    {
        $this->assertEqualsWithDelta(2 / sqrt(13), Cosine::similarity("👨‍👩‍👧‍👦", "👨👩👧👦"), 1e-9);
    }

    public function testNFKCCompatibilityNormalization()
    {
        $this->assertEquals(0.0, Cosine::similarity("①", "1"));
        $this->assertEquals(0.0, Cosine::similarity("ﬀ", "ff"));
        $this->assertEquals(0.0, Cosine::similarity("Å", "Å"));
    }

    public function testNFKDDecomposedNormalization()
    {
        $this->assertEquals(0.0, Cosine::similarity("Å", "A\u{030A}"));
        $this->assertEquals(0.0, Cosine::similarity("ñ", "n\u{0303}"));
    }

    public function testInvalidUtf8Input()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Input strings must be valid UTF-8.");
        $invalid = "\xFF\xFF";
        Cosine::similarity($invalid, "test");
    }
}
