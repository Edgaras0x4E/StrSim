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

        return ($normA && $normB) ? min(1.0, $dot / sqrt($normA * $normB)) : 0.0;
    }

    private static function countMbChars(string $str): array {
        return array_count_values(mb_str_split($str, 1, 'UTF-8'));
    }

    public static function similarityFromVectors(array $vecA, array $vecB): float {
        if (count($vecA) !== count($vecB)) {
            throw new \InvalidArgumentException("Vectors must be the same length.");
        }

        if (array_diff_key($vecA, $vecB)) {
            $vecA = array_values($vecA);
            $vecB = array_values($vecB);
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

        if ($normA != 0.0 && $normB != 0.0) {
            $similarity = $dot / (sqrt($normA) * sqrt($normB));
            return is_nan($similarity) ? $similarity : max(-1.0, min(1.0, $similarity));
        }

        return 0.0;
    }
}
