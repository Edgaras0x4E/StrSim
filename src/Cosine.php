<?php
namespace Edgaras\StrSim;

class Cosine {
    public static function similarity(string $a, string $b): float {
        $tokensA = count_chars($a, 1);
        $tokensB = count_chars($b, 1);
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
