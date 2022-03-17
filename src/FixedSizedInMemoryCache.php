<?php
declare(strict_types=1);

namespace Ropi\InMemoryCache;

class FixedSizedInMemoryCache extends InMemoryCache
{
    /**
     * @var \Iterator[]
     */
    public array $entries = [];

    public function __construct(
        null|int|\DateInterval $defaultTtl = null,
        private int $maxSize = 4096
    ) {
        parent::__construct($defaultTtl);
    }

    public function getMaxSize(): int
    {
        return $this->maxSize;
    }

    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        if ($this->count() >= $this->maxSize) {
            throw new \OverflowException(
                'Can not add cache entry with key "'
                . $key
                . '" to fixed sized in-memory cache, because limit of '
                . $this->maxSize
                . ' is reached',
                1632168251
            );
        }

        return parent::set($key, $value);
    }
}
