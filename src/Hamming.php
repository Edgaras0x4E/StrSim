<?php
namespace Edgaras\StrSim;

class Hamming {
    public static function distance(string $a, string $b): int {
        $lenA = mb_strlen($a, 'UTF-8');
        $lenB = mb_strlen($b, 'UTF-8');
        
        if ($lenA !== $lenB) {
            throw new \InvalidArgumentException("Strings must be of equal length.");
        }

        $distance = 0;
        for ($i = 0; $i < $lenA; $i++) {
            if (mb_substr($a, $i, 1, 'UTF-8') !== mb_substr($b, $i, 1, 'UTF-8')) {
                $distance++;
            }
        } 

        return $distance;
    }
}
