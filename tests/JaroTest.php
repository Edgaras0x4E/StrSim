<?php

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use Edgaras\StrSim\Jaro;

class JaroTest extends TestCase
{
    public function testIdenticalStrings()
    {
        $this->assertEqualsWithDelta(1.0, Jaro::distance("martha", "martha"), 1e-10);
    }

    public function testCompletelyDifferentStrings()
    {
        $this->assertEqualsWithDelta(0.0, Jaro::distance("abc", "xyz"), 1e-10);
    }

    public function testPartialMatch()
    {
        $expected = 0.9444444444;
        $actual = Jaro::distance("martha", "marhta");
        $this->assertEqualsWithDelta($expected, $actual, 1e-6);
    }

    public function testCaseWithSomeOverlap()
    {
        $expected = 0.8222222222;
        $actual = Jaro::distance("dwayne", "duane");
        $this->assertEqualsWithDelta($expected, $actual, 1e-6);
    }

    public function testEmptyStrings()
    {
        $this->assertEqualsWithDelta(1.0, Jaro::distance("", ""), 1e-10);
    }

    public function testOneEmptyString()
    {
        $this->assertEqualsWithDelta(0.0, Jaro::distance("abc", ""), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaro::distance("", "abc"), 1e-10);
    }

    public function testSingleCharacterMismatch()
    {
        $this->assertEqualsWithDelta(0.0, Jaro::distance("a", "b"), 1e-10);
    }

    public function testSingleCharacterMatch()
    {
        $this->assertEqualsWithDelta(1.0, Jaro::distance("a", "a"), 1e-10);
    }

    public function testMultibyteIdenticalStrings()
    {
        $this->assertEqualsWithDelta(1.0, Jaro::distance("café", "café"), 1e-10);
        $this->assertEqualsWithDelta(1.0, Jaro::distance("🚀🌟", "🚀🌟"), 1e-10);
    }

    public function testMultibytePartialMatch()
    {
        $result = Jaro::distance("café", "caffé");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testJapaneseCharacters()
    {
        $result = Jaro::distance("こんにちは", "こんにちわ");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testEmojiSupport()
    {
        $this->assertEqualsWithDelta(1.0, Jaro::distance("🚀", "🚀"), 1e-10);
        $result = Jaro::distance("🚀🌟", "🚀⭐");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testCyrillicCharacters()
    {
        $result = Jaro::distance("собака", "собаки");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testHebrewCharacters()
    {
        $result = Jaro::distance("עברית", "עבדית");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testMixedAsciiMultibyte()
    {
        $result = Jaro::distance("hello café", "hello cafe");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testMultibyteCompletelyDifferent()
    {
        $this->assertEqualsWithDelta(0.0, Jaro::distance("café", "🚀🌟"), 1e-10);
    }

    public function testMultibyteWithEmptyString()
    {
        $this->assertEqualsWithDelta(0.0, Jaro::distance("café", ""), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaro::distance("🚀🌟", ""), 1e-10);
    }

    public function testSingleMultibyteCharacter()
    {
        $this->assertEqualsWithDelta(1.0, Jaro::distance("é", "é"), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaro::distance("é", "ö"), 1e-10);
        $this->assertEqualsWithDelta(1.0, Jaro::distance("🚀", "🚀"), 1e-10);
    }

    public function testWindowBoundaryCases()
    {
        $this->assertEqualsWithDelta(0.0, Jaro::distance("a", "b"), 1e-10);
        $this->assertEqualsWithDelta(1.0, Jaro::distance("a", "a"), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaro::distance("ab", "cd"), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaro::distance("ab", "ba"), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaro::distance("abcd", "efgh"), 1e-10);
    }

    public function testExactJaroValues()
    {
        $this->assertEqualsWithDelta(0.0, Jaro::distance("abc", "xyz"), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaro::distance("a", "b"), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaro::distance("ab", "cd"), 1e-10);
        $result = Jaro::distance("abcdef", "fedcba");
        $this->assertGreaterThan(0.0, $result);
        $this->assertLessThan(1.0, $result);
    }

    public function testInvalidUtf8Input()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Input strings must be valid UTF-8.");
        $invalid = "\xFF\xFF";
        Jaro::distance($invalid, "test");
    }
}