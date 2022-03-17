<?php
declare(strict_types=1);

namespace Ropi\InMemoryCache;

class InMemoryCache implements InMemoryCacheInterface
{
    /**
     * @var object[]
     */
    protected array $entries = [];

    /**
     * @var callable[]
     */
    private array $deleteHandlers = [];

    public function __construct(
        private null|int|\DateInterval $defaultTtl = null
    ) {}

    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        $this->entries[$key] = (object) [
            'value' => $value,
            'expiry' => $this->calculateExpiry($ttl === null ? $this->defaultTtl : $ttl)
        ];

        return true;
    }

    public function has(string $key): bool
    {
        $entry = $this->entries[$key] ?? null;

        if (!$entry) {
            return false;
        }

        if ($this->expired($entry)) {
            $this->delete($key);
            return false;
        }

        return true;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $entry = $this->entries[$key] ?? null;

        if (!$entry) {
            return $default;
        }

        if ($this->expired($entry)) {
            $this->delete($key);
            return $default;
        }

        return $entry->value;
    }

    public function delete(string $key): bool
    {
        if (isset($this->entries[$key])) {
            $this->invokeDeleteHandlers($key, $this->entries[$key]);
        }

        unset($this->entries[$key]);

        return true;
    }

    public function clear(): bool
    {
        foreach ($this->entries as $key => $entry) {
            $this->invokeDeleteHandlers($key, $entry);
            unset($this->entries[$key]);
        }

        return true;
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        foreach ($keys as $key) {
            yield $key => $this->get($key, $default);
        }
    }

    public function setMultiple(iterable $values, null|int|\DateInterval $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }

        return true;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return true;
    }

    public function onDelete(callable $callback): void
    {
        $this->deleteHandlers[] = $callback;
    }

    public function count(): int
    {
        return count($this->entries);
    }

    protected function invokeDeleteHandlers(string $key, object $entry): void
    {
        foreach ($this->deleteHandlers as $removeHandler) {
            $removeHandler($key, $entry->value);
        }
    }

    protected function calculateExpiry(null|int|\DateInterval $ttl): int
    {
        if (!$ttl) {
            return 0;
        }

        if ($ttl instanceof \DateInterval) {
            return (new \DateTime())->add($ttl)->getTimestamp();
        }

        return time() + $ttl;
    }

    protected function expired(object $entry): bool
    {
        if (!$entry->expiry) {
            return false;
        }

        return time() > $entry->expiry;
    }
}
