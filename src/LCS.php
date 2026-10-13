<?php
namespace Edgaras\StrSim;

class LCS {
    public static function length(string $a, string $b): int {
        if (!mb_check_encoding($a, 'UTF-8') || !mb_check_encoding($b, 'UTF-8')) {
            throw new \InvalidArgumentException("Input strings must be valid UTF-8.");
        }

        $charsA = mb_str_split($a, 1, 'UTF-8');
        $charsB = mb_str_split($b, 1, 'UTF-8');
        $m = count($charsA);
        $n = count($charsB);

        $prev = array_fill(0, $n + 1, 0);

        for ($i = 1; $i <= $m; $i++) {
            $cur = [0];
            $charA = $charsA[$i - 1];
            for ($j = 1; $j <= $n; $j++) {
                if ($charA === $charsB[$j - 1]) {
                    $cur[$j] = $prev[$j - 1] + 1;
                } else {
                    $cur[$j] = max($prev[$j], $cur[$j - 1]);
                }
            }
            $prev = $cur;
        }

        return $prev[$n];
    }
}
