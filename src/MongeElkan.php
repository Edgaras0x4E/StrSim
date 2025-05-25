<?php
namespace Edgaras\StrSim;

class MongeElkan {
    public static function similarity(string $a, string $b): float {
        $wordsA = explode(' ', $a);
        $wordsB = explode(' ', $b);
        $total = 0.0;

        foreach ($wordsA as $wa) {
            $maxSim = 0.0;
            foreach ($wordsB as $wb) {
                $sim = JaroWinkler::distance($wa, $wb);
                $maxSim = max($maxSim, $sim);
            }
            $total += $maxSim;
        }

        return $total / count($wordsA);
    }
}
