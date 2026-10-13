<?php
namespace Edgaras\StrSim;

class NeedlemanWunsch {

    public static function score(string $a, string $b, int $match = 1, int $mismatch = -1, int $gap = -1): int {

        if (!mb_check_encoding($a, 'UTF-8') || !mb_check_encoding($b, 'UTF-8')) {
            throw new \InvalidArgumentException("Input strings must be valid UTF-8.");
        }

        $charsA = mb_str_split($a, 1, 'UTF-8');
        $charsB = mb_str_split($b, 1, 'UTF-8');
        $m = count($charsA);
        $n = count($charsB);

        $prev = [];
        for ($j = 0; $j <= $n; $j++) $prev[$j] = $j * $gap;

        for ($i = 1; $i <= $m; $i++) {
            $cur = [$i * $gap];
            $charA = $charsA[$i - 1];
            for ($j = 1; $j <= $n; $j++) {
                $score = ($charA === $charsB[$j - 1]) ? $match : $mismatch;
                $cur[$j] = max(
                    $prev[$j - 1] + $score,
                    $prev[$j] + $gap,
                    $cur[$j - 1] + $gap
                );
            }
            $prev = $cur;
        }

        return $prev[$n];
    }
}
