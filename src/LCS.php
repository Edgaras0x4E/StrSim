<?php
namespace Edgaras\StrSim;

class LCS {
    public static function length(string $a, string $b): int {
        if (!mb_check_encoding($a, 'UTF-8') || !mb_check_encoding($b, 'UTF-8')) {
            throw new \InvalidArgumentException("Input strings must be valid UTF-8.");
        }
        
        $m = mb_strlen($a, 'UTF-8');
        $n = mb_strlen($b, 'UTF-8');
        $dp = [];

        for ($i = 0; $i <= $m; $i++) {
            for ($j = 0; $j <= $n; $j++) {
                if ($i === 0 || $j === 0) {
                    $dp[$i][$j] = 0;
                } elseif (mb_substr($a, $i - 1, 1, 'UTF-8') === mb_substr($b, $j - 1, 1, 'UTF-8')) {
                    $dp[$i][$j] = $dp[$i - 1][$j - 1] + 1;
                } else {
                    $dp[$i][$j] = max($dp[$i - 1][$j], $dp[$i][$j - 1]);
                }
            }
        }

        return $dp[$m][$n];
    }
}
