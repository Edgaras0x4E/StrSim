<?php
namespace Edgaras\StrSim;

class LCS {
    public static function length(string $a, string $b): int {
        $m = strlen($a);
        $n = strlen($b);
        $dp = [];

        for ($i = 0; $i <= $m; $i++) {
            for ($j = 0; $j <= $n; $j++) {
                if ($i === 0 || $j === 0) {
                    $dp[$i][$j] = 0;
                } elseif ($a[$i - 1] === $b[$j - 1]) {
                    $dp[$i][$j] = $dp[$i - 1][$j - 1] + 1;
                } else {
                    $dp[$i][$j] = max($dp[$i - 1][$j], $dp[$i][$j - 1]);
                }
            }
        }

        return $dp[$m][$n];
    }
}
