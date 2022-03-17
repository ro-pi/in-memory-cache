<?php
declare(strict_types=1);

namespace Ropi\InMemoryCache;

use Psr\SimpleCache\CacheInterface;

interface InMemoryCacheInterface extends CacheInterface
{
    function count(): int;
    function onDelete(callable $callback): void;
}
