<?php
namespace Edgaras\StrSim;

class Levenshtein {
    public static function distance(string $a, string $b): int {
        if (!mb_check_encoding($a, 'UTF-8') || !mb_check_encoding($b, 'UTF-8')) {
            throw new \InvalidArgumentException("Input strings must be valid UTF-8.");
        }

        $charsA = mb_str_split($a, 1, 'UTF-8');
        $charsB = mb_str_split($b, 1, 'UTF-8');
        $lenA = count($charsA);
        $lenB = count($charsB);

        if ($lenA === 0) return $lenB;
        if ($lenB === 0) return $lenA;

        $prev = range(0, $lenB);

        for ($i = 1; $i <= $lenA; $i++) {
            $cur = [$i];
            $charA = $charsA[$i - 1];
            for ($j = 1; $j <= $lenB; $j++) {
                $cost = ($charA === $charsB[$j - 1]) ? 0 : 1;

                $cur[$j] = min(
                    $prev[$j] + 1,
                    $cur[$j - 1] + 1,
                    $prev[$j - 1] + $cost
                );
            }
            $prev = $cur;
        }

        return $prev[$lenB];
    }
}
