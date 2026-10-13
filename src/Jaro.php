<?php
namespace Edgaras\StrSim;

class Jaro {

    public static function similarity(string $s1, string $s2): float {
        if (!mb_check_encoding($s1, 'UTF-8') || !mb_check_encoding($s2, 'UTF-8')) {
            throw new \InvalidArgumentException("Input strings must be valid UTF-8.");
        }

        $chars1 = mb_str_split($s1, 1, 'UTF-8');
        $chars2 = mb_str_split($s2, 1, 'UTF-8');
        $len1 = count($chars1);
        $len2 = count($chars2);
        if ($len1 === 0 && $len2 === 0) return 1.0;

        $matchDistance = max((int)(max($len1, $len2) / 2) - 1, 0);
        $s1Matches = array_fill(0, $len1, false);
        $s2Matches = array_fill(0, $len2, false);

        $matches = $transpositions = 0;

        for ($i = 0; $i < $len1; $i++) {
            $start = max(0, $i - $matchDistance);
            $end = min($i + $matchDistance + 1, $len2);

            for ($j = $start; $j < $end; $j++) {
                if ($s2Matches[$j]) continue;
                if ($chars1[$i] !== $chars2[$j]) continue;
                $s1Matches[$i] = $s2Matches[$j] = true;
                $matches++;
                break;
            }
        }

        if ($matches === 0) return 0.0;

        $k = 0;
        for ($i = 0; $i < $len1; $i++) {
            if (!$s1Matches[$i]) continue;
            while (!$s2Matches[$k]) $k++;
            if ($chars1[$i] !== $chars2[$k]) $transpositions++;
            $k++;
        }

        return (($matches / $len1) + ($matches / $len2) + (($matches - $transpositions / 2) / $matches)) / 3.0;
    }

    public static function distance(string $s1, string $s2): float {
        return 1.0 - self::similarity($s1, $s2);
    }
}
