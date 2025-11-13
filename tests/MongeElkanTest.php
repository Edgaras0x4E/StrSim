<?php

declare(strict_types=1);

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Edgaras\StrSim\MongeElkan;
use Edgaras\StrSim\Jaro;
use Edgaras\StrSim\JaroWinkler;

#[CoversClass(MongeElkan::class)]
#[UsesClass(JaroWinkler::class)]
#[UsesClass(Jaro::class)]
class MongeElkanTest extends TestCase
{
    public function testIdenticalSentences(): void
    {
        $this->assertEqualsWithDelta(1.0, MongeElkan::similarity("john smith", "john smith"), 1e-10);
    }

    public function testPartialMatch(): void
    {
        $a = "john smith";
        $b = "jon smythe";

        $result = MongeElkan::similarity($a, $b);
        $this->assertGreaterThan(0.0, $result);
        $this->assertLessThan(1.0, $result);
    }

    public function testDifferentWords(): void
    {
        $this->assertEqualsWithDelta(0.0, MongeElkan::similarity("abc def", "xyz uvw"), 1e-10);
    }

    public function testSingleWordMatch(): void
    {
        $this->assertEqualsWithDelta(1.0, MongeElkan::similarity("hello", "hello"), 1e-10);
    }

    public function testSingleWordMismatch(): void
    {
        $similarity = MongeElkan::similarity("hello", "world");
        $this->assertGreaterThan(0.0, $similarity);
        $this->assertLessThan(1.0, $similarity);
    }

    public function testEmptyStrings(): void
    {
        $this->assertEqualsWithDelta(1.0, MongeElkan::similarity("", ""), 1e-10);
    }

    public function testOneEmpty(): void
    {
        $this->assertEqualsWithDelta(0.0, MongeElkan::similarity("test", ""), 1e-10);
    }

    public function testMultibyteIdenticalSentences(): void
    {
        $this->assertEqualsWithDelta(1.0, MongeElkan::similarity("café latte", "café latte"), 1e-10);
        $this->assertEqualsWithDelta(1.0, MongeElkan::similarity("🚀 🌟", "🚀 🌟"), 1e-10);
    }

    public function testMultibytePartialMatch(): void
    {
        $result = MongeElkan::similarity("café latte", "cafe latte");
        $this->assertGreaterThan(0.0, $result);
        $this->assertLessThan(1.0, $result);
    }

    public function testJapaneseWords(): void
    {
        $result = MongeElkan::similarity("こんにちは 世界", "こんにちは せかい");
        $this->assertGreaterThan(0.0, $result);
        $this->assertLessThan(1.0, $result);
    }

    public function testCyrillicWords(): void
    {
        $result = MongeElkan::similarity("собака кошка", "собака медведь");
        $this->assertGreaterThan(0.0, $result);
        $this->assertLessThan(1.0, $result);
    }

    public function testHebrewWords(): void
    {
        $result = MongeElkan::similarity("עברית טוב", "עבדית טוב");
        $this->assertGreaterThan(0.0, $result);
        $this->assertLessThan(1.0, $result);
    }

    public function testEmojiWords(): void
    {
        $result = MongeElkan::similarity("🚀 🌟", "🚀 ⭐");
        $this->assertGreaterThan(0.0, $result);
        $this->assertLessThan(1.0, $result);
    }

    public function testMixedAsciiMultibyte(): void
    {
        $result = MongeElkan::similarity("hello café", "hello cafe");
        $this->assertGreaterThan(0.0, $result);
        $this->assertLessThan(1.0, $result);
    }

    public function testSingleMultibyteWord(): void
    {
        $this->assertEqualsWithDelta(1.0, MongeElkan::similarity("café", "café"), 1e-10);
        $result = MongeElkan::similarity("café", "cafe");
        $this->assertGreaterThan(0.0, $result);
        $this->assertLessThan(1.0, $result);
    }

    public function testMultibyteDifferentWords(): void
    {
        $result = MongeElkan::similarity("café latte", "🚀 🌟");
        $this->assertGreaterThanOrEqual(0.0, $result);
        $this->assertLessThan(1.0, $result);
    }

    public function testMultibyteWithEmptyString(): void
    {
        $this->assertEqualsWithDelta(0.0, MongeElkan::similarity("café latte", ""), 1e-10);
    }

    public function testMultipleMultibyteWords(): void
    {
        $result = MongeElkan::similarity("こんにちは 世界 コンピューター", "こんにちわ 世界 コンピュータ");
        $this->assertGreaterThan(0.0, $result);
        $this->assertLessThan(1.0, $result);
    }

    public function testAsymmetryBehavior(): void
    {
        $a = MongeElkan::similarity("john", "john smith");
        $b = MongeElkan::similarity("john smith", "john");
        $this->assertNotEquals($a, $b);
    }

    public function testMultipleSpacesAndEmptyTokens(): void
    {
        $result = MongeElkan::similarity("hello  world", "hello world");
        $this->assertGreaterThan(0.0, $result);
        $this->assertLessThanOrEqual(1.0, $result);
    }
}
