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

class ReadmeExamplesTest extends TestCase
{
    public static function examples(): array
    {
        return [
            'Levenshtein kitten/sitting' => [Levenshtein::class, 'distance', ["kitten", "sitting"], 3],
            'DamerauLevenshtein abcd/acbd' => [DamerauLevenshtein::class, 'distance', ["abcd", "acbd"], 1],
            'Hamming bits' => [Hamming::class, 'distance', ["1011101", "1001001"], 2],
            'Jaro similarity dixon/dicksonx' => [Jaro::class, 'similarity', ["dixon", "dicksonx"], 0.767],
            'Jaro distance dixon/dicksonx' => [Jaro::class, 'distance', ["dixon", "dicksonx"], 0.233],
            'JaroWinkler similarity martha/marhta' => [JaroWinkler::class, 'similarity', ["martha", "marhta"], 0.961],
            'JaroWinkler distance martha/marhta' => [JaroWinkler::class, 'distance', ["martha", "marhta"], 0.039],
            'LCS DNA' => [LCS::class, 'length', ["ACCGGTCGAGTGCGCGGAAGCCGGCCGAA", "GTCGTTCGGAATGCCGTTGCTCTGTAAA"], 20],
            'SmithWaterman local alignment' => [SmithWaterman::class, 'score', ["ACACACTA", "AGCACACA"], 12],
            'NeedlemanWunsch global alignment' => [NeedlemanWunsch::class, 'score', ["GATTACA", "GCATGCU"], 0],
            'Cosine night/nacht' => [Cosine::class, 'similarity', ["night", "nacht"], 0.6],
            'Cosine vectors' => [Cosine::class, 'similarityFromVectors', [[0.1, 0.2, 0.3], [0.1, 0.3, 0.4]], 0.996],
            'Jaccard abc/bcd' => [Jaccard::class, 'index', ["abc", "bcd"], 0.5],
            'MongeElkan names' => [MongeElkan::class, 'similarity', ["john smith", "jon smythe"], 0.896],

            'Levenshtein accented' => [Levenshtein::class, 'distance', ["café", "caffe"], 2],
            'Levenshtein Japanese' => [Levenshtein::class, 'distance', ["こんにちは", "こんにちわ"], 1],
            'Levenshtein emoji' => [Levenshtein::class, 'distance', ["\u{1F680}\u{1F31F}", "\u{1F680}\u{2B50}"], 1],
            'Hamming skin tones' => [Hamming::class, 'distance', ["\u{1F44D}\u{1F3FD}", "\u{1F44D}\u{1F3FE}"], 1],
            'Jaro similarity Cyrillic' => [Jaro::class, 'similarity', ["привет", "привет"], 1.0],
            'Jaro distance Cyrillic' => [Jaro::class, 'distance', ["привет", "привет"], 0.0],
            'JaroWinkler similarity Arabic' => [JaroWinkler::class, 'similarity', ["عربي", "عربى"], 0.883],
            'JaroWinkler distance Arabic' => [JaroWinkler::class, 'distance', ["عربي", "عربى"], 0.117],
            'Levenshtein ZWJ family' => [
                Levenshtein::class,
                'distance',
                ["\u{1F468}\u{200D}\u{1F469}\u{200D}\u{1F467}\u{200D}\u{1F466}", "\u{1F468}\u{1F469}\u{1F467}\u{1F466}"],
                3,
            ],
            'Levenshtein combining mark' => [Levenshtein::class, 'distance', ["\u{00E9}", "e\u{0301}"], 2],

            'SmithWaterman custom' => [SmithWaterman::class, 'score', ["ACGT", "ACGT", 5, -2, -1], 20],
            'NeedlemanWunsch custom' => [NeedlemanWunsch::class, 'score', ["ACGT", "ACGT", 3, -1, -2], 12],
        ];
    }

    #[DataProvider('examples')]
    public function testReadmeExample(string $class, string $method, array $args, int|float $expected)
    {
        $actual = $class::$method(...$args);

        if (is_int($expected)) {
            $this->assertSame($expected, $actual);
        } else {
            $this->assertEqualsWithDelta($expected, $actual, 0.0005);
        }
    }

    public function testErrorHandlingHammingUnequalLength()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Strings must be of equal length.");
        Hamming::distance("abc", "abcd");
    }

    public function testErrorHandlingInvalidUtf8()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Input strings must be valid UTF-8.");
        Levenshtein::distance("valid", "\xFF\xFF");
    }

    public function testErrorHandlingVectorLength()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Vectors must be the same length.");
        Cosine::similarityFromVectors([1, 2], [1, 2, 3]);
    }
}
