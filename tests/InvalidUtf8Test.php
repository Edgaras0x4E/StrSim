<?php

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Edgaras\StrSim\Levenshtein;
use Edgaras\StrSim\DamerauLevenshtein;
use Edgaras\StrSim\Hamming;
use Edgaras\StrSim\Jaro;
use Edgaras\StrSim\JaroWinkler;
use Edgaras\StrSim\LCS;
use Edgaras\StrSim\SmithWaterman;
use Edgaras\StrSim\NeedlemanWunsch;
use Edgaras\StrSim\Cosine;
use Edgaras\StrSim\Jaccard;
use Edgaras\StrSim\MongeElkan;

class InvalidUtf8Test extends TestCase
{
    private const METHODS = [
        [Levenshtein::class, 'distance'],
        [DamerauLevenshtein::class, 'distance'],
        [Hamming::class, 'distance'],
        [LCS::class, 'length'],
        [Jaro::class, 'similarity'],
        [Jaro::class, 'distance'],
        [JaroWinkler::class, 'similarity'],
        [JaroWinkler::class, 'distance'],
        [MongeElkan::class, 'similarity'],
        [SmithWaterman::class, 'score'],
        [NeedlemanWunsch::class, 'score'],
        [Cosine::class, 'similarity'],
        [Jaccard::class, 'index'],
    ];

    public static function methodsAndPositions(): array
    {
        $cases = [];
        foreach (self::METHODS as [$class, $method]) {
            $name = substr(strrchr($class, '\\'), 1) . '::' . $method;
            $cases[$name . ' first argument'] = [$class, $method, "\xFF\xFF", "ab"];
            $cases[$name . ' second argument'] = [$class, $method, "ab", "\xFF\xFF"];
        }
        return $cases;
    }

    #[DataProvider('methodsAndPositions')]
    public function testRejectsInvalidUtf8(string $class, string $method, string $a, string $b)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Input strings must be valid UTF-8.");
        $class::$method($a, $b);
    }

    public static function invalidSequences(): array
    {
        return [
            'invalid byte' => ["\xFF"],
            'lone continuation byte' => ["\x80"],
            'truncated sequence' => ["\xE2\x82"],
            'overlong encoding' => ["\xC0\xAF"],
            'UTF-16 surrogate' => ["\xED\xA0\x80"],
        ];
    }

    #[DataProvider('invalidSequences')]
    public function testHammingRejectsInvalidSequenceBeforeLengthCheck(string $invalid)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Input strings must be valid UTF-8.");
        Hamming::distance($invalid, "x");
    }
}
