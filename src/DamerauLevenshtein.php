<?php
namespace Edgaras\StrSim;

class DamerauLevenshtein {
    public static function distance(string $a, string $b): int {
        if (!mb_check_encoding($a, 'UTF-8') || !mb_check_encoding($b, 'UTF-8')) {
            throw new \InvalidArgumentException("Input strings must be valid UTF-8.");
        }
        
        $lenA = mb_strlen($a, 'UTF-8');
        $lenB = mb_strlen($b, 'UTF-8');
        $dp = [];

        for ($i = 0; $i <= $lenA; $i++) $dp[$i][0] = $i;
        for ($j = 0; $j <= $lenB; $j++) $dp[0][$j] = $j;

        for ($i = 1; $i <= $lenA; $i++) {
            for ($j = 1; $j <= $lenB; $j++) {
                $charA = mb_substr($a, $i - 1, 1, 'UTF-8');
                $charB = mb_substr($b, $j - 1, 1, 'UTF-8');
                $cost = ($charA === $charB) ? 0 : 1;
                
                $dp[$i][$j] = min(
                    $dp[$i - 1][$j] + 1,
                    $dp[$i][$j - 1] + 1,
                    $dp[$i - 1][$j - 1] + $cost
                );
                
                if ($i > 1 && $j > 1) {
                    $charA2 = mb_substr($a, $i - 2, 1, 'UTF-8');
                    $charB2 = mb_substr($b, $j - 2, 1, 'UTF-8');
                    if ($charA === $charB2 && $charA2 === $charB) {
                        $dp[$i][$j] = min($dp[$i][$j], $dp[$i - 2][$j - 2] + $cost);
                    }
                }
            }
        }

        return $dp[$lenA][$lenB];
    }
}
