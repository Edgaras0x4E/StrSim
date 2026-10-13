<?php
namespace Edgaras\StrSim;

class Hamming {
    public static function distance(string $a, string $b): int {
        if (!mb_check_encoding($a, 'UTF-8') || !mb_check_encoding($b, 'UTF-8')) {
            throw new \InvalidArgumentException("Input strings must be valid UTF-8.");
        }

        $charsA = mb_str_split($a, 1, 'UTF-8');
        $charsB = mb_str_split($b, 1, 'UTF-8');
        $length = count($charsA);

        if ($length !== count($charsB)) {
            throw new \InvalidArgumentException("Strings must be of equal length.");
        }

        $distance = 0;
        for ($i = 0; $i < $length; $i++) {
            if ($charsA[$i] !== $charsB[$i]) {
                $distance++;
            }
        }

        return $distance;
    }
}
