<?php
namespace Edgaras\StrSim;

class Levenshtein {
    public static function distance(string $a, string $b): int {
        return levenshtein($a, $b);
    }
}