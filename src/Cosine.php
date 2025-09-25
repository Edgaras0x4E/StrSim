<?php
namespace Edgaras\StrSim;

class Cosine {
    public static function similarity(string $a, string $b): float {
        if (!mb_check_encoding($a, 'UTF-8') || !mb_check_encoding($b, 'UTF-8')) {
            throw new \InvalidArgumentException("Input strings must be valid UTF-8.");
        }
        
        $tokensA = self::countMbChars($a);
        $tokensB = self::countMbChars($b);
        $dot = 0;
        $normA = 0;
        $normB = 0;

        foreach ($tokensA as $k => $v) {
            $dot += $v * ($tokensB[$k] ?? 0);
            $normA += $v * $v;
        }

        foreach ($tokensB as $v) {
            $normB += $v * $v;
        }

        return ($normA && $normB) ? $dot / (sqrt($normA) * sqrt($normB)) : 0;
    }

    private static function countMbChars(string $str): array {
        $chars = [];
        $length = mb_strlen($str, 'UTF-8');
        
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($str, $i, 1, 'UTF-8');
            $chars[$char] = ($chars[$char] ?? 0) + 1;
        }
        
        return $chars;
    }

    public static function similarityFromVectors(array $vecA, array $vecB): float {
        if (count($vecA) !== count($vecB)) {
            throw new \InvalidArgumentException("Vectors must be the same length.");
        }

        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($vecA as $i => $valA) {
            $valB = $vecB[$i];
            $dot += $valA * $valB;
            $normA += $valA * $valA;
            $normB += $valB * $valB;
        }

        return ($normA && $normB) ? $dot / (sqrt($normA) * sqrt($normB)) : 0.0;
    }
}
