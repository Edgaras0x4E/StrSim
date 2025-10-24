<?php
namespace Edgaras\StrSim;

class MongeElkan {
    public static function similarity(string $a, string $b): float {
        if (!mb_check_encoding($a, 'UTF-8') || !mb_check_encoding($b, 'UTF-8')) {
            throw new \InvalidArgumentException("Input strings must be valid UTF-8.");
        }
        
        $wordsA = array_filter(preg_split('/\s+/u', trim($a)), function($word) { return $word !== ''; });
        $wordsB = array_filter(preg_split('/\s+/u', trim($b)), function($word) { return $word !== ''; });
         
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
