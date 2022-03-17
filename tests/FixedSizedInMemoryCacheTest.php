<?php
declare(strict_types=1);

namespace Ropi\InMemoryCache\Tests;

use PHPUnit\Framework\TestCase;
use Ropi\InMemoryCache\FixedSizedInMemoryCache;

class FixedSizedInMemoryCacheTest extends TestCase
{
    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function testBaseUsage()
    {
        $fixed = new FixedSizedInMemoryCache(null, 2);
        $this->assertEquals(2, $fixed->getMaxSize());

        $fixed->set('a', 10);
        $fixed->set('b', 20);
        $this->assertEquals(2, $fixed->count());

        $this->expectException(\OverflowException::class);
        $fixed->set('c', 30);
    }

    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function testMultiple()
    {
        $fixed = new FixedSizedInMemoryCache(null, 3);

        $fixed->setMultiple(['a' => 10, 'b' => 20]);
        $this->assertEquals(2, $fixed->count());

        $this->expectException(\OverflowException::class);
        $fixed->setMultiple(['c' => 30, 'd' => 40]);
    }

}
