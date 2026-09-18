# PHP Sorting Optimisations

#PHP

A video about [a sorting algorithm which shouldn't work](https://www.youtube.com/watch?v=ixXOUeBOPJw) came across [my
feed](/youtube-player-rebuild) recently (recommend anything Matt Parker is involved with btw). It left me wondering which
sorting algorithm PHP uses.

A quick look at the [source code](https://github.com/php/php-src/blob/PHP-8.5/Zend/zend_sort.c) reveals the answer is: it
depends. specifically on how many things you want to sort. The short version is:

- 0-1 things: no sorting
- 2-5 things: [network](https://en.wikipedia.org/wiki/Sorting_network) and [insertion sort](https://en.wikipedia.org/wiki/Insertion_sort) hybrids
- 6-16 things: [insertion sort](https://en.wikipedia.org/wiki/Insertion_sort)
- \>16 things: hybrid of [introsort](https://en.wikipedia.org/wiki/Introsort) and [quick sort](/php-quick-sort)

To understand it better I wanted to write a PHP port of the C source:

```php
function phpSort(array $array): array {
    return match(count($array)) {
        0 => $array,
        1 => $array,
        2 => networkSort2($array),
        3 => networkSort3($array),
        4 => networkInsertionHydridSort4($array),
        5 => networkInsertionHybridSort5($array),
        6 => insertionSort($array),
        7 => insertionSort($array),
        8 => insertionSort($array),
        9 => insertionSort($array),
        10 => insertionSort($array),
        11 => insertionSort($array),
        12 => insertionSort($array),
        13 => insertionSort($array),
        14 => insertionSort($array),
        15 => insertionSort($array),
        16 => insertionSort($array),
        default => introQuickHybridSort($array),
    }
}

function swap(array $array, int $i, int $j): array {
    [$array[$i], $array[$j]] = [$array[$j], $array[$i]];
    
    return $array;
}

function networkSort2(array $array): array {
    if ($array[0] > $array[1]) {
        return swap($array, 1, 0);
    }

    return $array;
}

function networkSort3(array $array): array {
    if ($array[0] <= $array[1]) {
        if ($array[1] <= $array[2]) {
            return $array;
        }
        
        $array = swap($array, 1, 2);
        
        if ($array[0] > $array[1]) {
            $array = swap($array, 0, 1);
        }
        
        return $array;
    }
    
    if ($array[2] <= $array[1]) {
        return swap($array, 0, 2);
    }
    
    $array = swap($array, 0, 1);
    
    if ($array[1] > $array[2]) {
        $array = swap($array, 1, 2);
    }
    
    return $array;
}

function networkInsertionHydridSort4(array $array): array {
    $array = networkSort3($array);
    
    if ($array[2] > $array[3]) {
        $array = swap($array, 2, 3);
        
        if ($array[1] > $array[2]) {
            $array = swap($array, 1, 2);
            
            if ($array[0] > $array[1]) {
                $array = swap($array, 0, 1);
            }
        }
    }
    
    return $array;
}

function networkInsertionHybridSort5(array $array): array {
    $array = networkInsertionHydridSort4($array);
    
    if ($array[3] > $array[4]) {
        $array = swap($array, 3, 4);
        
        if ($array[2] > $array[3]) {
            $array = swap($array, 2, 3);
            
            if ($array[1] > $array[2]) {
                $array = swap($array, 1, 2);
                
                if ($array[0] > $array[1]) {
                    $array = swap($array, 0, 1);
                }
            }
        }
    }
    
    return $array;
}
```

