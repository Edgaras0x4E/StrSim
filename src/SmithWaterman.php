<?php
namespace Edgaras\StrSim;

class SmithWaterman {
    public static function score(string $a, string $b, int $match = 2, int $mismatch = -1, int $gap = -1): int {
        if (!mb_check_encoding($a, 'UTF-8') || !mb_check_encoding($b, 'UTF-8')) {
            throw new \InvalidArgumentException("Input strings must be valid UTF-8.");
        }

        $charsA = mb_str_split($a, 1, 'UTF-8');
        $charsB = mb_str_split($b, 1, 'UTF-8');
        $m = count($charsA);
        $n = count($charsB);

        $prev = array_fill(0, $n + 1, 0);
        $max = 0;

        for ($i = 1; $i <= $m; $i++) {
            $cur = [0];
            $charA = $charsA[$i - 1];
            for ($j = 1; $j <= $n; $j++) {
                $score = ($charA === $charsB[$j - 1]) ? $match : $mismatch;
                $cur[$j] = max(
                    0,
                    $prev[$j - 1] + $score,
                    $prev[$j] + $gap,
                    $cur[$j - 1] + $gap
                );
                $max = max($max, $cur[$j]);
            }
            $prev = $cur;
        }

        return $max;
    }
}
