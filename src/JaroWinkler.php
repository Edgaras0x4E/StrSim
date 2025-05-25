<?php
namespace Edgaras\StrSim;

class JaroWinkler {
    public static function distance(string $s1, string $s2, float $prefixScale = 0.1): float {
        $jaro = Jaro::distance($s1, $s2);
        $prefix = 0;
        $maxPrefix = 4;

        for ($i = 0; $i < min($maxPrefix, strlen($s1), strlen($s2)); $i++) {
            if ($s1[$i] === $s2[$i]) $prefix++;
            else break;
        }

        return $jaro + $prefix * $prefixScale * (1 - $jaro);
    }
}
