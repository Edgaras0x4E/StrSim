<?php
namespace Edgaras\StrSim;

class SmithWaterman {
    public static function score(string $a, string $b, int $match = 2, int $mismatch = -1, int $gap = -1): int {
        $m = strlen($a);
        $n = strlen($b);
        $dp = array_fill(0, $m + 1, array_fill(0, $n + 1, 0));
        $max = 0;

        for ($i = 1; $i <= $m; $i++) {
            for ($j = 1; $j <= $n; $j++) {
                $score = ($a[$i - 1] === $b[$j - 1]) ? $match : $mismatch;
                $dp[$i][$j] = max(
                    0,
                    $dp[$i - 1][$j - 1] + $score,
                    $dp[$i - 1][$j] + $gap,
                    $dp[$i][$j - 1] + $gap
                );
                $max = max($max, $dp[$i][$j]);
            }
        }

        return $max;
    }
}
