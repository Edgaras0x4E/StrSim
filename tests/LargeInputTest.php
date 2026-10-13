<?php

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use Edgaras\StrSim\Levenshtein;
use Edgaras\StrSim\DamerauLevenshtein;
use Edgaras\StrSim\LCS;
use Edgaras\StrSim\SmithWaterman;
use Edgaras\StrSim\NeedlemanWunsch;

class LargeInputTest extends TestCase
{
    private const LENGTH = 2000;
    private const MAX_PEAK_BYTES = 8 * 1024 * 1024;

    private static function randomString(int $seed, array $alphabet): string
    {
        mt_srand($seed);
        $s = '';
        for ($i = 0; $i < self::LENGTH; $i++) {
            $s .= $alphabet[mt_rand(0, count($alphabet) - 1)];
        }
        return $s;
    }

    private function measurePeak(callable $fn): array
    {
        $before = memory_get_usage();
        memory_reset_peak_usage();
        $result = $fn();
        return [$result, memory_get_peak_usage() - $before];
    }

    public function testLevenshteinMatchesBuiltinWithLinearMemory()
    {
        $a = self::randomString(1, ['a', 'c', 'g', 't']);
        $b = self::randomString(2, ['a', 'c', 'g', 't']);

        [$result, $peak] = $this->measurePeak(fn() => Levenshtein::distance($a, $b));

        $this->assertSame(levenshtein($a, $b), $result);
        $this->assertLessThan(self::MAX_PEAK_BYTES, $peak);
    }

    public function testDamerauLevenshteinLinearMemory()
    {
        $a = self::randomString(3, ['a', 'c', 'é', '🚀']);
        $b = self::randomString(4, ['a', 'c', 'é', '🚀']);

        [$result, $peak] = $this->measurePeak(fn() => DamerauLevenshtein::distance($a, $b));

        $this->assertLessThanOrEqual(Levenshtein::distance($a, $b), $result);
        $this->assertLessThan(self::MAX_PEAK_BYTES, $peak);
    }

    public function testLcsLinearMemory()
    {
        $a = self::randomString(5, ['a', 'c', 'é', '🚀']);
        $b = self::randomString(6, ['a', 'c', 'é', '🚀']);

        [$result, $peak] = $this->measurePeak(fn() => LCS::length($a, $b));

        $d = Levenshtein::distance($a, $b);
        $this->assertGreaterThanOrEqual(self::LENGTH - $d, $result);
        $this->assertLessThanOrEqual(self::LENGTH - $d / 2, $result);
        $this->assertLessThan(self::MAX_PEAK_BYTES, $peak);
    }

    public function testSmithWatermanLinearMemory()
    {
        $a = self::randomString(7, ['a', 'c', 'é', '🚀']);

        [$result, $peak] = $this->measurePeak(fn() => SmithWaterman::score($a, $a));

        $this->assertSame(2 * self::LENGTH, $result);
        $this->assertLessThan(self::MAX_PEAK_BYTES, $peak);
    }

    public function testNeedlemanWunschLinearMemory()
    {
        $a = self::randomString(8, ['a', 'c', 'é', '🚀']);

        [$result, $peak] = $this->measurePeak(fn() => NeedlemanWunsch::score($a, $a));

        $this->assertSame(self::LENGTH, $result);
        $this->assertLessThan(self::MAX_PEAK_BYTES, $peak);
    }
}
