# PHP Quicksort 📚

#PHP
#sorting

I recently came across the video ["Quicksort Algorithm in Five Lines of Code"](https://www.youtube.com/watch?v=OKc2hAmMOY4)
from the excellent Computerphile channel, thoroughly recommend the channel by the way.

The first half of the video explains how the algorithm works, the second is about the actual code. As I was watching the
explanation I paused the video before it got to the code because I wanted to see if I could implement the algorithm in
PHP, and see how short I could reasonably get it.

First I outlined the steps of the algorithm:

1. Take array of values
2. Find the middle (pivot) value
3. Create two new arrays
   - 'left' which has numbers < pivot
   - 'right' which has numbers > pivot
4. goto 1 for each new array
5. Once there is only one number per array - recombine including the pivot


## First Attempt

This is honestly my first attempt. It almost worked first try :D as with most recursive functions you need to first eat
up a few GB of RAM with infinite recursion bugs 🪲

```php
function qsort(array $values): array {
    $valueCount = count($values);
    
    if ($valueCount <= 1) {
        return $values;
    }
    
    $pivot = $values[floor($valueCount/2)];
    $left = [];
    $right = [];

    foreach ($values as $value) {
        if ($value < $pivot) {
            $left[] = $value;
        }
        if ($value > $pivot) {
            $right[] = $value;
        }
    }
    
    return [...qsort($left), $pivot, ...qsort($right)];
}

qsort([3,1,2,4,10,8,-1]); // -1, 1, 2, 3, 4, 8, 10
```


## Can It Be Shorter?

The video showed that in Haskell it can be written in 5 lines of code. I don't believe it can be that short in PHP, but
just for the fun of it I tried to make it shorter while not going full code-golf and making it an unreadable mess.

This is what I got:

```php
function qsort(array $values): array {
    if (count($values) <= 1) { return $values; }
    
    $pivot = $values[floor(count($values)/2)];
    $left = array_filter($values, fn ($value): bool => $value <=> $pivot === -1);
    $right = array_filter($values, fn ($value): bool => $value <=> $pivot === 1);
    
    return [...qsort($left), $pivot, ...qsort($right)];
}
```

This uses `{php}array_filter()` to split the values, while this is more concise it means iterating over the values twice,
so it will likely be half as fast, I didn't test it, that's really not what this is about. It's shorter though :P

The shortest I could get without obviously damaging performance was this:

```php
function qsort(array $values): array {
    if (count($values) <= 1) { return $values; }
    
    $pivot = $values[floor(count($values)/2)];
    $left = $right = [];

    foreach ($values as $value) {
        ($value < $pivot) && $left[] = $value;
        ($value > $pivot) && $right[] = $value;
    }
    
    return [...qsort($left), $pivot, ...qsort($right)];
}
```


## Can AI Do Better? 🤖

No, In short. ChatGPT came up with almost exactly same solution as my first attempt. Any attempts to shorten or improve
on the solution were either syntactically invalid or started to stray away from the quick sort into other algorithms. 


## What About Non-numeric Input?

I thought that the function as written would only work for numerical values, but apparently PHPs inequality operators
work with all characters?! TIL!

```php
'a' < 'b' // true
'abc' < 'aca' // true
'php' > 'javascript' // true
```

To be honest I think using `{php}strcmp()` or the spaceship operator (`{php}<=>`) would be much more 'expected'.
