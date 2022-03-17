<?php
declare(strict_types=1);

namespace Ropi\InMemoryCache\Tests;

use PHPUnit\Framework\TestCase;
use Ropi\InMemoryCache\FifoInMemoryCache;

class FifoInMemoryCacheTest extends TestCase
{
    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function testBaseUsage()
    {
        $fifo = new FifoInMemoryCache(null, 3);
        $this->assertEquals(3, $fifo->getMaxSize());

        $fifo->set('a', 10);
        $fifo->set('b', 20);
        $fifo->set('c', 30);
        $this->assertEquals(3, $fifo->count());

        $fifo->set('d', 40);
        $this->assertEquals(3, $fifo->count());
        $this->assertEquals(40, $fifo->get('d'));
        $this->assertFalse($fifo->has('a'));
    }

    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function testMultiple()
    {
        $fifo = new FifoInMemoryCache(null, 3);

        $fifo->setMultiple(['a' => 10, 'b' => 20, 'c' => 30, 'd' => 40]);
        $this->assertEquals(3, $fifo->count());
        $this->assertEquals(20, $fifo->get('b'));
        $this->assertFalse($fifo->has('a'));
    }

}
