<?php

namespace Edgaras\StrSim\Tests;

use PHPUnit\Framework\TestCase;
use Edgaras\StrSim\Jaccard;

class JaccardTest extends TestCase
{
    public function testIdenticalStrings()
    {
        $this->assertEqualsWithDelta(1.0, Jaccard::index("abc", "abc"), 1e-10);
    }

    public function testCompletelyDifferentStrings()
    {
        $this->assertEqualsWithDelta(0.0, Jaccard::index("abc", "xyz"), 1e-10);
    }

    public function testPartiallyOverlappingStrings()
    {
        $this->assertEqualsWithDelta(0.5, Jaccard::index("abc", "bcd"), 1e-10);
        // a,b,c vs b,c,d → intersection = [b,c], union = [a,b,c,d]
    }

    public function testEmptyStrings()
    {
        $this->assertEqualsWithDelta(0.0, Jaccard::index("", ""), 1e-10);
    }

    public function testOneEmptyString()
    {
        $this->assertEqualsWithDelta(0.0, Jaccard::index("abc", ""), 1e-10);
        $this->assertEqualsWithDelta(0.0, Jaccard::index("", "xyz"), 1e-10);
    }

    public function testRepeatedCharacters()
    {
        $this->assertEqualsWithDelta(1.0, Jaccard::index("aaaa", "a"), 1e-10);
        // both resolve to set ['a']
    }
}
