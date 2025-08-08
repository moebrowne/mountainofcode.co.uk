# Simple Benchmarking in PHP ⏳

#PHP
#benchmark

Sometimes I want a quick and simple way to know how long a block of code took to run. The usual way you see it done is
something like this:

```php
$startTime = microtime(true);

// Code to test

$endTime = microtime(true);
$time = $endTime - $startTime;
```

`{php}$time` contains the number of seconds which it took for the code to run.

There is a simpler way:

```php
$time = -microtime(true);

// Code to test

$time += microtime(true);
```

This automatically calculates the difference between start and end without needing the extra step.

The same cane be done for memory usage:

```php
$memory = -memory_get_usage();

// Code to test

$memory += memory_get_usage();
```
