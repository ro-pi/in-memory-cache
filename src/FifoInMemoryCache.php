<?php
declare(strict_types=1);

namespace Ropi\InMemoryCache;

class FifoInMemoryCache extends InMemoryCache
{
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
            $this->delete(array_key_first($this->entries));
        }

        unset($this->entries[$key]); // Unset possible old entry to force fresh entry to be on top position

        return parent::set($key, $value);
    }
}
