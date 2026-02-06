<?php

declare(strict_types=1);

function pipe(mixed $value, callable ...$operations): mixed
{
    foreach ($operations as $name => $callback) {
        $callback = match (true) {
            is_int($name) => $callback,
            str_starts_with($name, 'filter') => fn() => array_filter($value, $callback),
            str_starts_with($name, 'map') => fn() => array_map($callback, $value),
            str_starts_with($name, 'find') => fn() => array_find($value, $callback),
            str_starts_with($name, 'sortBy') => function () use (&$value, $callback): array {
                usort($value, $callback);
                return $value;
            },
            str_starts_with($name, 'keyBy') => function() use ($callback, $value) {
                $result = [];

                array_walk(
                    $value,
                    function($value) use ($callback, &$result) {
                        $result[$callback($value)] = $value;
                    });

                return $result;
            },
            default => $callback,
        };

        $value = $callback($value);
    }

    return $value;
}

function e(string $string): string
{
    return htmlentities($string, ENT_QUOTES, 'UTF-8');
}
