<?php
namespace Edgaras\StrSim;

class NeedlemanWunsch {
    public static function score(string $a, string $b, int $match = 1, int $mismatch = -1, int $gap = -1): int {
        $m = strlen($a);
        $n = strlen($b);
        $dp = [];

        for ($i = 0; $i <= $m; $i++) $dp[$i][0] = $i * $gap;
        for ($j = 0; $j <= $n; $j++) $dp[0][$j] = $j * $gap;

        for ($i = 1; $i <= $m; $i++) {
            for ($j = 1; $j <= $n; $j++) {
                $score = ($a[$i - 1] === $b[$j - 1]) ? $match : $mismatch;
                $dp[$i][$j] = max(
                    $dp[$i - 1][$j - 1] + $score,
                    $dp[$i - 1][$j] + $gap,
                    $dp[$i][$j - 1] + $gap
                );
            }
        }

        return $dp[$m][$n];
    }
}
