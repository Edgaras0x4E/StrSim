<?php
namespace Edgaras\StrSim;

class Jaccard {
    public static function index(string $a, string $b): float {
        if (!mb_check_encoding($a, 'UTF-8') || !mb_check_encoding($b, 'UTF-8')) {
            throw new \InvalidArgumentException("Input strings must be valid UTF-8.");
        }

        $setA = array_flip(mb_str_split($a, 1, 'UTF-8'));
        $setB = array_flip(mb_str_split($b, 1, 'UTF-8'));
        $intersection = array_intersect_key($setA, $setB);
        $union = $setA + $setB;

        if (count($union) === 0) {
            return 0.0;
        }

        return count($intersection) / count($union);
    }
}
