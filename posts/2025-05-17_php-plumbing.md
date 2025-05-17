# 🛁 PHP Pipes

#PHP
#pipe
#functional


I really like unix pipes, they are easy to intuit. Data 'flows' from one process to another, the same concept has been
added to a number of programming languages over the years but not PHP.

There have been attempts to add it to the language ([v1](https://wiki.php.net/rfc/pipe-operator) & [v2](https://wiki.php.net/rfc/pipe-operator-v2))
but nothing has been implemented yet. There are [userland implementations](https://pipeline.thephpleague.com/) but they
are pretty unwieldy IMO.

**Pipe v3 RFC** - Since I began drafting this post there has been a [third RFC](https://wiki.php.net/rfc/pipe-operator-v3)
which looks like it's going to pass!

As it seems to be my thing lately, I wanted to see if I could make a small stupid simple native implementation.



## Attempt 1

```php
function pipe(mixed $value): object
{
    return new class ($value)
    {
        private array $callbacks = [];

        public function __construct(protected mixed $value)
        {
        }

        public function through(callable $callback): self
        {
            $this->callbacks[] = $callback;

            return $this;
        }

        public function go(): mixed
        {
            return array_reduce(
                $this->callbacks,
                fn (mixed $value, callable $callback): mixed => $callback($value),
                $this->value,
            );
        }
    };
}

$newValue = pipe('Something')
    ->through(fn (string $value): string => substr($value, 2))
    ->through(fn (string $value): string => strtoupper($value))
    ->go();
```

This worked, but I didn't like the call to `{php}go()` at the end. It could be changed so that each closure is executed
as `{php}through()` is called, but I abandoned this as the whole thing was a bit meh and I thought I could do better.



## Attempt 2

I ditched the class entirely and switched to a single [variadic](https://www.php.net/manual/en/functions.arguments.php#functions.variable-arg-list)
function.

```php
function pipe(mixed $value, callable ...$callables): mixed
{
    return array_reduce(
        $callables,
        fn (mixed $value, callable $callback): mixed => $callback($value),
        $value,
    );
}

$newValue = pipe(
    'Something',
    fn (string $value): string => substr($value, 2),
    fn (string $value): string => strtoupper($value),
);
```

If you fancy a quick round of code golf, you can use a short closure and ditch the mixed type:

```php
$pipe = fn ($value, callable ...$fns) => array_reduce($fns, fn ($value, callable $fn) => $fn($value), $value);
```



## Array Manipulation

I tested out the `{php}pipe()` function in my [RSS reader](https://github.com/moebrowne/feed-forager) project, it was
great except for this unreadable mess:

```php
$feedUrls = pipe(
    $feeds,
    fn (array $feeds): array => array_filter($feeds, fn (string $feedUrl): bool => str_starts_with($feedUrl, '#') === false),
    fn (array $feeds): array => array_map(fn (string $feedUrl): string => $feedUrl[0] === '*' ? substr($feedUrl, 1):$feedUrl, $feeds),
);
```

It could be easier to read if the nested short closures where expanded into anonymous functions, but I really wanted to
keep one operation per line if at all possible.

The root of the problem is that the array manipulation functions require a closure. I needed to mark the operations as
'array-acting'. What I came up with was to use named parameters. How does this help? The name can be used to add
<magic-sparkle>magic</magic-sparkle> to each operation.

The example from above now becomes:

```php

$feedUrls = pipe(
    $feeds,
    filterOutCommenttedUrls: fn (string $feedUrl): bool => str_starts_with($feedUrl, '#'),
    mapToRemoveLeadingStar: fn (string $feedUrl): string => ltrim($feedUrl, '*'),
);
```

```php
function pipe(mixed $value, callable ...$operations): mixed
{
    foreach ($operations as $name => $callback) {
        $callback = match (true) {
            is_int($name) => $callback,
            str_starts_with($name, 'filter') => fn () => array_filter($value, $callback),
            str_starts_with($name, 'map') => fn () => array_map($callback, $value),
            str_starts_with($name, 'sortBy') => function () use (&$value, $callback): array {
                usort($value, $callback);
                return $value;
            },
            default => $callback,
        };

        $value = $callback($value);
    }

    return $value;
}
```

This continues to work for non-array values, and I quite like that it adds a pseudo comment to each operation which adds
to the readability.

```php
$newValue = pipe(
    'Something',
    reduceStringLength: fn (string $value): string => substr($value, 2),
    transformToUppercase: fn (string $value): string => strtoupper($value),
);
```

The thing I don't like about this is that it requires prior knowledge to understand that the filter, map and sortBy
prefixes are special. I also feel like this is going against the intended behaviour for named arguments, aka it's a
hack.



## Limitations

There are a couple of limitations which I have come across:

1. Argument names can't be re-used
2. Static analysis can't understand the magic 
3. Named arguments can't be followed by unnamed ones. This is not allowed:

```php
$newValue = pipe(
    'Something',
    transformToUppercase: strtoupper(...), // named argument
    trim(...), // ❌ unnamed argument
);
```



## Conditional Operations

If you need to conditionally add/remove operations to the pipe, you must build an array and then spread them:

```php
$fns = [
    'filterOutComments' => fn (string $line): bool => str_starts_with($line, '#') === false,
];

if ($someLogic) {
    $fns['mapToRemoveLeadingStar'] = fn (string $feedUrl): string => ltrim($feedUrl, '*'),
}

$feedUrlsToFetch = pipe($feedUrls, ...$fns);
```


