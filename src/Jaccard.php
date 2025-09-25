<?php
namespace Edgaras\StrSim;

class Jaccard {
    public static function index(string $a, string $b): float {
        if (!mb_check_encoding($a, 'UTF-8') || !mb_check_encoding($b, 'UTF-8')) {
            throw new \InvalidArgumentException("Input strings must be valid UTF-8.");
        }
        
        $setA = array_unique(self::mbStrSplit($a));
        $setB = array_unique(self::mbStrSplit($b));
        $intersection = array_intersect($setA, $setB);
        $union = array_unique(array_merge($setA, $setB));

        if (count($union) === 0) {
            return 0.0;
        }

        return count($intersection) / count($union);
    }

    private static function mbStrSplit(string $str): array {
        $chars = [];
        $length = mb_strlen($str, 'UTF-8');
        
        for ($i = 0; $i < $length; $i++) {
            $chars[] = mb_substr($str, $i, 1, 'UTF-8');
        }
        
        return $chars;
    }
}
