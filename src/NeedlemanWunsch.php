<?php
namespace Edgaras\StrSim;

class NeedlemanWunsch {
    
    public static function score(string $a, string $b, int $match = 1, int $mismatch = -1, int $gap = -1): int {

        if (!mb_check_encoding($a, 'UTF-8') || !mb_check_encoding($b, 'UTF-8')) {
            throw new \InvalidArgumentException("Input strings must be valid UTF-8.");
        }

        $m = mb_strlen($a, 'UTF-8');
        $n = mb_strlen($b, 'UTF-8');
        $dp = [];

        for ($i = 0; $i <= $m; $i++) $dp[$i][0] = $i * $gap;
        for ($j = 0; $j <= $n; $j++) $dp[0][$j] = $j * $gap;

        for ($i = 1; $i <= $m; $i++) {
            $charA = mb_substr($a, $i - 1, 1, 'UTF-8');
            for ($j = 1; $j <= $n; $j++) {
                $charB = mb_substr($b, $j - 1, 1, 'UTF-8');
                $score = ($charA === $charB) ? $match : $mismatch;
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
