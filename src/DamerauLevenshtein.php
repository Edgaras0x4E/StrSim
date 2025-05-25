<?php
namespace Edgaras\StrSim;

class DamerauLevenshtein {
    public static function distance(string $a, string $b): int {
        $lenA = strlen($a);
        $lenB = strlen($b);
        $dp = [];

        for ($i = 0; $i <= $lenA; $i++) $dp[$i][0] = $i;
        for ($j = 0; $j <= $lenB; $j++) $dp[0][$j] = $j;

        for ($i = 1; $i <= $lenA; $i++) {
            for ($j = 1; $j <= $lenB; $j++) {
                $cost = ($a[$i - 1] === $b[$j - 1]) ? 0 : 1;
                $dp[$i][$j] = min(
                    $dp[$i - 1][$j] + 1,
                    $dp[$i][$j - 1] + 1,
                    $dp[$i - 1][$j - 1] + $cost
                );
                if ($i > 1 && $j > 1 && $a[$i - 1] === $b[$j - 2] && $a[$i - 2] === $b[$j - 1]) {
                    $dp[$i][$j] = min($dp[$i][$j], $dp[$i - 2][$j - 2] + $cost);
                }
            }
        }

        return $dp[$lenA][$lenB];
    }
}
