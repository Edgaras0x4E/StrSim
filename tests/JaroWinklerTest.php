<?php

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use Edgaras\StrSim\Jaro;
use Edgaras\StrSim\JaroWinkler;

class JaroWinklerTest extends TestCase
{
    public function testIdenticalStrings()
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("martha", "martha"), 1e-10);
    }

    public function testCompletelyDifferentStrings()
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("abc", "xyz"), 1e-10);
    }

    public function testKnownPairMARTHAvsMARHTA()
    {
        $jaro = Jaro::distance("martha", "marhta");  
        $prefix = 3;
        $expected = $jaro + $prefix * 0.1 * (1 - $jaro);
        $actual = JaroWinkler::distance("martha", "marhta");
        $this->assertEqualsWithDelta($expected, $actual, 1e-6);
    }

    public function testPrefixLimit()
    { 
        $a = "prefix_match_1";
        $b = "prefix_match_2";

        $jaro = Jaro::distance($a, $b);
        $expected = $jaro + 4 * 0.1 * (1 - $jaro);  
        $actual = JaroWinkler::distance($a, $b);
        $this->assertEqualsWithDelta($expected, $actual, 1e-6);
    }

    public function testNoCommonPrefix()
    {
        $a = "xxxxx";
        $b = "yyyyy";
        $jaro = Jaro::distance($a, $b);
        $expected = $jaro;
        $this->assertEqualsWithDelta($expected, JaroWinkler::distance($a, $b), 1e-10);
    }

    public function testEmptyStrings()
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("", ""), 1e-10);
    }

    public function testOneEmptyString()
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("abc", ""), 1e-10);
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("", "xyz"), 1e-10);
    }

    public function testSingleCharMatch()
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("a", "a"), 1e-10);
    }

    public function testSingleCharMismatch()
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("a", "b"), 1e-10);
    }

    public function testMultibyteIdenticalStrings()
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("café", "café"), 1e-10);
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("🚀🌟", "🚀🌟"), 1e-10);
    }

    public function testMultibytePartialMatch()
    {
        $result = JaroWinkler::distance("café", "caffé");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testJapaneseCharacters()
    {
        $result = JaroWinkler::distance("こんにちは", "こんにちわ");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testEmojiSupport()
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("🚀", "🚀"), 1e-10);
        $this->assertEqualsWithDelta(Jaro::distance("🚀🌟", "🚀⭐") + 0.1 * (1 - Jaro::distance("🚀🌟", "🚀⭐")), JaroWinkler::distance("🚀🌟", "🚀⭐"), 1e-6);
    }

    public function testCyrillicCharacters()
    {
        $result = JaroWinkler::distance("собака", "собаки");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testHebrewCharacters()
    {
        $result = JaroWinkler::distance("עברית", "עבדית");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testMixedAsciiMultibyte()
    {
        $result = JaroWinkler::distance("hello café", "hello cafe");
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(1, $result);
    }

    public function testMultibyteCompletelyDifferent()
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("café", "🚀🌟"), 1e-10);
    }

    public function testMultibyteWithEmptyString()
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("café", ""), 1e-10);
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("🚀🌟", ""), 1e-10);
    }

    public function testSingleMultibyteCharacter()
    {
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("é", "é"), 1e-10);
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("é", "ö"), 1e-10);
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("🚀", "🚀"), 1e-10);
    }

    public function testMultibytePrefix()
    {
        $withoutPrefix = JaroWinkler::distance("ébcdef", "éxydef");
        $withPrefix = JaroWinkler::distance("éébcdef", "ééxydef");
        $this->assertGreaterThan($withoutPrefix, $withPrefix);
    }

    public function testMultibytePrefixScale()
    {
        $defaultScale = JaroWinkler::distance("café", "cafe");
        $customScale = JaroWinkler::distance("café", "cafe", 0.2);
        $this->assertNotEquals($defaultScale, $customScale);
    }

    public function testPrefixScaleValidation()
    {
        $base = Jaro::distance("martha", "marhta");
        $scale1 = JaroWinkler::distance("martha", "marhta", 0.1);
        $scale2 = JaroWinkler::distance("martha", "marhta", 0.2);
        $expected1 = $base + 3 * 0.1 * (1 - $base);
        $expected2 = $base + 3 * 0.2 * (1 - $base);
        $this->assertEqualsWithDelta($expected1, $scale1, 1e-6);
        $this->assertEqualsWithDelta($expected2, $scale2, 1e-6);
        $this->assertGreaterThan($scale1, $scale2);
    }

    public function testWindowBoundaryCases()
    {
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("a", "b"), 1e-10);
        $this->assertEqualsWithDelta(1.0, JaroWinkler::distance("a", "a"), 1e-10);
        $this->assertEqualsWithDelta(0.0, JaroWinkler::distance("ab", "cd"), 1e-10);
        $result = JaroWinkler::distance("ab", "ba");
        $this->assertEqualsWithDelta(0.0, $result, 1e-10);
    }

    public function testInvalidUtf8Input()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Input strings must be valid UTF-8.");
        $invalid = "\xFF\xFF";
        JaroWinkler::distance($invalid, "test");
    }
}