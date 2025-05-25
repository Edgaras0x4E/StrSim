<?php
namespace Edgaras\StrSim;

class Hamming {
    public static function distance(string $a, string $b): int {
        if (strlen($a) !== strlen($b)) {
            throw new \Exception("Strings must be of equal length.");
        }

        $distance = 0;
        for ($i = 0; $i < strlen($a); $i++) {
            if ($a[$i] !== $b[$i]) $distance++;
        } 

        return $distance;
    }
}
