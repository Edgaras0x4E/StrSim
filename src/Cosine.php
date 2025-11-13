<?php

declare(strict_types=1);

namespace Edgaras\StrSim;

class Cosine
{
    public static function similarity(string $a, string $b): float
    {
        if (!mb_check_encoding($a, 'UTF-8') || !mb_check_encoding($b, 'UTF-8')) {
            throw new \InvalidArgumentException("Input strings must be valid UTF-8.");
        }

        $tokensA = self::countMbChars($a);
        $tokensB = self::countMbChars($b);
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($tokensA as $k => $v) {
            $dot += (float)$v * (float)($tokensB[$k] ?? 0);
            $normA += (float)$v * (float)$v;
        }

        foreach ($tokensB as $v) {
            $normB += (float)$v * (float)$v;
        }

        return ($normA && $normB) ? $dot / (sqrt($normA) * sqrt($normB)) : 0;
    }

    /**
     * @return array<string, int>
     */
    private static function countMbChars(string $str): array
    {
        $chars = [];
        $length = mb_strlen($str, 'UTF-8');

        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($str, $i, 1, 'UTF-8');
            $chars[$char] = ($chars[$char] ?? 0) + 1;
        }

        return $chars;
    }

    /**
     * @param array<int|string, int|float> $vecA
     * @param array<int|string, int|float> $vecB
     */
    public static function similarityFromVectors(array $vecA, array $vecB): float
    {
        if (count($vecA) !== count($vecB)) {
            throw new \InvalidArgumentException("Vectors must be the same length.");
        }

        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($vecA as $i => $valA) {
            $valB = $vecB[$i];
            $dot += (float)$valA * (float)$valB;
            $normA += (float)$valA * (float)$valA;
            $normB += (float)$valB * (float)$valB;
        }

        return ($normA && $normB) ? $dot / (sqrt($normA) * sqrt($normB)) : 0.0;
    }
}
