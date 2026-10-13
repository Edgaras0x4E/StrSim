<?php
namespace Edgaras\StrSim;

class DamerauLevenshtein {
    public static function distance(string $a, string $b): int {
        if (!mb_check_encoding($a, 'UTF-8') || !mb_check_encoding($b, 'UTF-8')) {
            throw new \InvalidArgumentException("Input strings must be valid UTF-8.");
        }

        $charsA = mb_str_split($a, 1, 'UTF-8');
        $charsB = mb_str_split($b, 1, 'UTF-8');
        $lenA = count($charsA);
        $lenB = count($charsB);

        $prev2 = [];
        $prev = range(0, $lenB);

        for ($i = 1; $i <= $lenA; $i++) {
            $cur = [$i];
            $charA = $charsA[$i - 1];
            for ($j = 1; $j <= $lenB; $j++) {
                $charB = $charsB[$j - 1];
                $cost = ($charA === $charB) ? 0 : 1;

                $cur[$j] = min(
                    $prev[$j] + 1,
                    $cur[$j - 1] + 1,
                    $prev[$j - 1] + $cost
                );

                if ($i > 1 && $j > 1 && $charA === $charsB[$j - 2] && $charsA[$i - 2] === $charB) {
                    $cur[$j] = min($cur[$j], $prev2[$j - 2] + $cost);
                }
            }
            $prev2 = $prev;
            $prev = $cur;
        }

        return $prev[$lenB];
    }
}
