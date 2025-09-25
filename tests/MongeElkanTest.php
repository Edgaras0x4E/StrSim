<?php

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use Edgaras\StrSim\MongeElkan;

class MongeElkanTest extends TestCase
{
    public function testIdenticalSentences()
    {
        $this->assertEqualsWithDelta(1.0, MongeElkan::similarity("john smith", "john smith"), 1e-10);
    }

    public function testPartialMatch()
    {
        $a = "john smith";
        $b = "jon smythe";

        $result = MongeElkan::similarity($a, $b);
        $this->assertGreaterThan(0.0, $result);
        $this->assertLessThan(1.0, $result);
    }

    public function testDifferentWords()
    {
        $this->assertEqualsWithDelta(0.0, MongeElkan::similarity("abc def", "xyz uvw"), 1e-10);
    }

    public function testSingleWordMatch()
    {
        $this->assertEqualsWithDelta(1.0, MongeElkan::similarity("hello", "hello"), 1e-10);
    }

    public function testSingleWordMismatch()
    {
        $similarity = MongeElkan::similarity("hello", "world");
        $this->assertGreaterThan(0.0, $similarity);
        $this->assertLessThan(1.0, $similarity);
    }

    public function testEmptyStrings()
    {
        $this->assertEqualsWithDelta(0.0, MongeElkan::similarity("", ""), 1e-10);
    }

    public function testOneEmpty()
    {
        $this->assertEqualsWithDelta(0.0, MongeElkan::similarity("test", ""), 1e-10);
    }

    public function testMultibyteIdenticalSentences()
    {
        $this->assertEqualsWithDelta(1.0, MongeElkan::similarity("café latte", "café latte"), 1e-10);
        $this->assertEqualsWithDelta(1.0, MongeElkan::similarity("🚀 🌟", "🚀 🌟"), 1e-10);
    }

    public function testMultibytePartialMatch()
    {
        $result = MongeElkan::similarity("café latte", "cafe latte");
        $this->assertGreaterThan(0.0, $result);
        $this->assertLessThan(1.0, $result);
    }

    public function testJapaneseWords()
    {
        $result = MongeElkan::similarity("こんにちは 世界", "こんにちは せかい");
        $this->assertGreaterThan(0.0, $result);
        $this->assertLessThan(1.0, $result);
    }

    public function testCyrillicWords()
    {
        $result = MongeElkan::similarity("собака кошка", "собака медведь");
        $this->assertGreaterThan(0.0, $result);
        $this->assertLessThan(1.0, $result);
    }

    public function testHebrewWords()
    {
        $result = MongeElkan::similarity("עברית טוב", "עבדית טוב");
        $this->assertGreaterThan(0.0, $result);
        $this->assertLessThan(1.0, $result);
    }

    public function testEmojiWords()
    {
        $result = MongeElkan::similarity("🚀 🌟", "🚀 ⭐");
        $this->assertGreaterThan(0.0, $result);
        $this->assertLessThan(1.0, $result);
    }

    public function testMixedAsciiMultibyte()
    {
        $result = MongeElkan::similarity("hello café", "hello cafe");
        $this->assertGreaterThan(0.0, $result);
        $this->assertLessThan(1.0, $result);
    }

    public function testSingleMultibyteWord()
    {
        $this->assertEqualsWithDelta(1.0, MongeElkan::similarity("café", "café"), 1e-10);
        $result = MongeElkan::similarity("café", "cafe");
        $this->assertGreaterThan(0.0, $result);
        $this->assertLessThan(1.0, $result);
    }

    public function testMultibyteDifferentWords()
    {
        $result = MongeElkan::similarity("café latte", "🚀 🌟");
        $this->assertGreaterThanOrEqual(0.0, $result);
        $this->assertLessThan(1.0, $result);
    }

    public function testMultibyteWithEmptyString()
    {
        $this->assertEqualsWithDelta(0.0, MongeElkan::similarity("café latte", ""), 1e-10);
    }

    public function testMultipleMultibyteWords()
    {
        $result = MongeElkan::similarity("こんにちは 世界 コンピューター", "こんにちわ 世界 コンピュータ");
        $this->assertGreaterThan(0.0, $result);
        $this->assertLessThan(1.0, $result);
    }

    public function testAsymmetryBehavior()
    {
        $a = MongeElkan::similarity("john", "john smith");
        $b = MongeElkan::similarity("john smith", "john");
        $this->assertNotEquals($a, $b);
    }

    public function testMultipleSpacesAndEmptyTokens()
    {
        $result = MongeElkan::similarity("hello  world", "hello world");
        $this->assertGreaterThan(0.0, $result);
        $this->assertLessThanOrEqual(1.0, $result);
    }

    public function testWhitespaceNormalization()
    {
        $this->assertEqualsWithDelta(1.0, MongeElkan::similarity("hello world", "hello\tworld"), 1e-10);
        $this->assertEqualsWithDelta(1.0, MongeElkan::similarity("hello world", "hello\nworld"), 1e-10);
        $this->assertEqualsWithDelta(1.0, MongeElkan::similarity("hello world", "hello\r\nworld"), 1e-10);
        $this->assertEqualsWithDelta(1.0, MongeElkan::similarity("hello world", "hello   world"), 1e-10);
    }

    public function testLeadingTrailingWhitespace()
    {
        $this->assertEqualsWithDelta(1.0, MongeElkan::similarity("hello world", " hello world "), 1e-10);
        $this->assertEqualsWithDelta(1.0, MongeElkan::similarity("hello world", "\thello world\t"), 1e-10);
        $this->assertEqualsWithDelta(1.0, MongeElkan::similarity("hello world", "\nhello world\n"), 1e-10);
    }

    public function testEmptyStringHandling()
    {
        $this->assertEqualsWithDelta(0.0, MongeElkan::similarity("", ""), 1e-10);
        $this->assertEqualsWithDelta(0.0, MongeElkan::similarity("hello", ""), 1e-10);
        $this->assertEqualsWithDelta(0.0, MongeElkan::similarity("", "hello"), 1e-10);
        $this->assertEqualsWithDelta(0.0, MongeElkan::similarity("   ", "hello"), 1e-10);
        $this->assertEqualsWithDelta(0.0, MongeElkan::similarity("hello", "   "), 1e-10);
    }

    public function testPunctuationTokenization()
    {
        $result = MongeElkan::similarity("hello-world", "hello world");
        $this->assertGreaterThan(0.0, $result);
        $this->assertLessThan(1.0, $result);
        $result = MongeElkan::similarity("hello.world", "hello world");
        $this->assertGreaterThan(0.0, $result);
        $this->assertLessThan(1.0, $result);
    }

    public function testMixedWhitespaceTypes()
    {
        $this->assertEqualsWithDelta(1.0, MongeElkan::similarity("a b c", "a\tb\nc"), 1e-10);
        $this->assertEqualsWithDelta(1.0, MongeElkan::similarity("a b c", "a\r\nb\tc"), 1e-10);
    }

    public function testUnicodeWhitespace()
    {
        $this->assertEqualsWithDelta(1.0, MongeElkan::similarity("hello world", "hello\u{00A0}world"), 1e-10);
        $this->assertEqualsWithDelta(1.0, MongeElkan::similarity("hello world", "hello\u{2000}world"), 1e-10);
    }

    public function testInvalidUtf8Input()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Input strings must be valid UTF-8.");
        $invalid = "\xFF\xFF";
        MongeElkan::similarity($invalid, "test");
    }
}
