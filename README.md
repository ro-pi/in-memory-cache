# PSR-16 compatible in-memory cache library for PHP

This PHP-based library provides different in-memory caches.

### Requirements
* PHP ^8.0
## Installation
The library can be installed from a command line interface by using [composer](https://getcomposer.org/).

```
composer require ropi/in-memory-cache
```
## First In – First Out (FIFO)
```php
<?php
$cache = new \Ropi\InMemoryCache\FifoInMemoryCache(defaultTtl: null, maxSize: 3);

$cache->set('a', 10);
$cache->set('b', 20);
$cache->set('c', 30);

var_dump($cache->get('b')); // Prints 20 
var_dump($cache->count()); // Prints 3

$cache->set('d', 40);

var_dump($cache->count()); // Still prints 3, because cache size is limited to 3 and thus first cache entry was deleted
var_dump($cache->get('a')); // Prints NULL, because 'a' was the first cache entry
var_dump($cache->get('d')); // Prints 40
```

## Fixed sized
```php
<?php
$cache = new \Ropi\InMemoryCache\FixedSizedInMemoryCache(defaultTtl: null, maxSize: 3);

$cache->set('a', 10);
$cache->set('b', 20);
$cache->set('c', 30);

var_dump($cache->get('b')); // Prints 20 
var_dump($cache->count()); // Prints 3

$cache->set('d', 40); // Throws OverflowException, because cache size is limited to 3
```

## Time-to-live (TTL)
```php
<?php
$cache = new \Ropi\InMemoryCache\InMemoryCache(defaultTtl: 2);

$cache->set('a', 10);

var_dump($cache->get('a')); // Prints 10 

sleep(3);

var_dump($cache->get('a')); // Prints NULL, because TTL expired
```
