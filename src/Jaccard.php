<?php
namespace Edgaras\StrSim;

class Jaccard {
    public static function index(string $a, string $b): float {
        $setA = array_unique(str_split($a));
        $setB = array_unique(str_split($b));
        $intersection = array_intersect($setA, $setB);
        $union = array_unique(array_merge($setA, $setB));

        if (count($union) === 0) {
            return 0.0;
        }

        return count($intersection) / count($union);
    }
}
