<?php
namespace Edgaras\StrSim;

class JaroWinkler {
    public static function distance(string $s1, string $s2, float $prefixScale = 0.1): float {
        if (!mb_check_encoding($s1, 'UTF-8') || !mb_check_encoding($s2, 'UTF-8')) {
            throw new \InvalidArgumentException("Input strings must be valid UTF-8.");
        }
        
        $jaro = Jaro::distance($s1, $s2);
        $prefix = 0;
        $maxPrefix = 4;

        for ($i = 0; $i < min($maxPrefix, mb_strlen($s1, 'UTF-8'), mb_strlen($s2, 'UTF-8')); $i++) {
            if (mb_substr($s1, $i, 1, 'UTF-8') === mb_substr($s2, $i, 1, 'UTF-8')) $prefix++;
            else break;
        }

        return $jaro + $prefix * $prefixScale * (1 - $jaro);
    }
}
