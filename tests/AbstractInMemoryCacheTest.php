<?php
declare(strict_types=1);

namespace Ropi\InMemoryCache\Tests;

use PHPUnit\Framework\TestCase;
use Ropi\InMemoryCache\InMemoryCache;

class AbstractInMemoryCacheTest extends TestCase
{
    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function testBaseUsage()
    {
        $cache = new InMemoryCache();

        $this->assertTrue($cache->set('a', 10));
        $this->assertEquals(1, $cache->count());
        $this->assertEquals(10, $cache->get('a'));
        $this->assertTrue($cache->has('a'));

        $this->assertTrue($cache->set('b', 20));
        $this->assertEquals(2, $cache->count());
        $this->assertEquals(20, $cache->get('b'));

        $this->assertTrue($cache->set('c', 30));
        $this->assertEquals(3, $cache->count());
        $this->assertEquals(30, $cache->get('c'));

        $this->assertTrue($cache->set('d', 50));
        $this->assertEquals(50, $cache->get('d'));

        $this->assertTrue($cache->delete('d'));
        $this->assertNull($cache->get('d'));
        $this->assertEquals('my-default-value', $cache->get('d', 'my-default-value'));

        $this->assertTrue($cache->clear());
        $this->assertEquals(0, $cache->count());
    }

    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function testMultiple()
    {
        $cache = new InMemoryCache();

        $this->assertTrue($cache->setMultiple(['a' => 10, 'b' => 20, 'c' => 30]));
        $this->assertEquals(3, $cache->count());
        $this->assertEquals(20, $cache->get('b'));
        $this->assertTrue($cache->has('a'));

        $this->assertTrue($cache->deleteMultiple(['b', 'c', 'unknown-key']));
        $this->assertEquals(1, $cache->count());

        foreach ($cache->getMultiple(['b', 'a', 'unknown-key'], 'default-value') as $key => $value) {
            if ($key === 'a') {
                $this->assertEquals(10, $value);
            } else {
                $this->assertEquals('default-value', $value);
            }
        }
    }

    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function testDeleteHandlers()
    {
        $cache = new InMemoryCache(2);

        $handledKeys = [];
        $handledValues = [];

        $cache->onDelete(function(string $key, mixed $value) use (&$handledKeys, &$handledValues) {
            $handledKeys[] = $key;
            $handledValues[] = $value;
        });

        $cache->set('a', 'v1');
        $cache->set('b', 'v2');
        $cache->set('c', 'v3');

        $cache->delete('b');

        $this->assertEquals(['b'], $handledKeys);
        $this->assertEquals(['v2'], $handledValues);

        $cache->deleteMultiple(['a', 'b', 'c']);

        $this->assertEquals(['b', 'a', 'c'], $handledKeys);
        $this->assertEquals(['v2', 'v1', 'v3'], $handledValues);
    }

    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function testTtl()
    {
        $cache = new InMemoryCache(2);

        $cache->set('a', 10);
        $this->assertTrue($cache->has('a'));
        $this->assertEquals(10, $cache->get('a'));

        $cache->set('b', 20, 4);

        $numDeleteHandlers = 0;
        $cache->onDelete(function(string $key, mixed $value) use (&$numDeleteHandlers) {
            $this->assertEquals('a', $key);
            $this->assertEquals(10, $value);
            $numDeleteHandlers++;
        });

        sleep(3);

        $this->assertFalse($cache->has('a'));
        $this->assertNull($cache->get('a'));
        $this->assertEquals(20, $cache->get('b'));
        $this->assertEquals(1, $numDeleteHandlers);
    }
}
