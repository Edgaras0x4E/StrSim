<?php

declare(strict_types=1);

namespace Edgaras\StrSim;

class MongeElkan
{
    public static function similarity(string $a, string $b): float
    {
        if (!mb_check_encoding($a, 'UTF-8') || !mb_check_encoding($b, 'UTF-8')) {
            throw new \InvalidArgumentException("Input strings must be valid UTF-8.");
        }

        $splitA = preg_split('/\s+/u', trim($a));
        $splitB = preg_split('/\s+/u', trim($b));

        if ($splitA === false || $splitB === false) {
            throw new \RuntimeException("Failed to split strings.");
        }

        $wordsA = array_filter($splitA, function (string $word): bool { return $word !== ''; });
        $wordsB = array_filter($splitB, function (string $word): bool { return $word !== ''; });

        if (count($wordsA) === 0 && count($wordsB) === 0) {
            return 1.0;
        }

        if (count($wordsA) === 0 || count($wordsB) === 0) {
            return 0.0;
        }

        $total = 0.0;

        foreach ($wordsA as $wa) {
            $maxSim = 0.0;
            foreach ($wordsB as $wb) {
                $sim = JaroWinkler::similarity($wa, $wb);
                $maxSim = max($maxSim, $sim);
            }
            $total += $maxSim;
        }

        return $total / count($wordsA);
    }
}
