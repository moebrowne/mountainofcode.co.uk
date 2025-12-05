# Bloom Filter 🌼

#bloom

Bloom filters are very cool. They're one of the concepts which, I think, to fully internalise you need to implement it
yourself. This is my toy implementation. This is not a how-to or guide. If you're looking for a deep-dive, I suggest
reading [Scott Helme's post on the subject](https://scotthelme.co.uk/frequency-analysis-on-hundreds-of-billions-of-reports-at-report-uri-bloom-filters/)


## 1. A New Class

Create a class to represent the filter:

```php
class Bloom {
    private(set) array $bits = [];

    public function __construct(int $length) {
        $this->bits = array_fill(0, $length, 0);
    }

    public function __toString()
    {
        return implode('', $this->bits);
    }
}
```

I'm going to represent the filter value as an array of `0` and `1` ints because that makes it easy to see what is going
on. A real implementation would use binary maths.

I want to add string values to the filter, so a string-to-binary method is required:

```php
private function stringToBinary(string $string) {
    $binary = [];

    for ($i = 0; $i < strlen($string); $i++) {
        $binaryDigits = str_split(sprintf("%08b", ord($string[$i])));

        foreach ($binaryDigits as $binaryDigit) {
            $binary[] = (int)$binaryDigit;
        }
    }

    return $binary;
}

implode('', stringToBinary('test')); // 01110100011001010111001101110100
```

## 2. Adding Values

Next we need to implement an add method. Adding a value to the filter requires taking all `1` bits of the new value and
setting the corresponding but in the filter to `1`. Some of them may already be `1`, more on that later.

```php
public function add(string $string): void
{
    foreach ($this->stringToBinaryArray($string) as $index => $bit) {
        if ($bit === 1) {
            $this->bits[$index] = 1;
        }
    }
}
```

```php
$bloom = new Bloom(40);

$bloom->add('hello'); // 0110100001100101011011000110110001101111
$bloom->add('bingo'); // 0110101001101101011011100110111101101111
$bloom->add('spark'); // 0111101101111101011011110111111101101111
```

Each new item in the filter adds more and more high bits. Highlighting the changes shows it more clearly:

```
.11.1....11..1.1.11.11...11.11...11.1111
......1.....1.........1.......11........
...1...1...1...........1...1............
```


## 3. Querying

Next we need a way of querying the filter to see if a value is contained in the filter. This is simply a case of checking
that all the high bits in the given value are also set in the filter:

```php
public function contains(string $string): bool
{
    foreach ($this->stringToBinaryArray($string) as $index => $bit) {
        if ($bit === 1 && $this->bits[$index] === 0) {
            return false;
        }
    }

    return true;
}
```

```php
$bloom = new Bloom(40);

$bloom->add('hello');
$bloom->add('bingo');
$bloom->add('spark');

$bloom->contains('hello'); // true
$bloom->contains('bingo'); // true
$bloom->contains('spark'); // true
$bloom->contains('doggo'); // false
```


## 4. False Positives

This is where bloom filters are a bit odd. The contains method can only tell us if a value is either definitely not in
the filter or that it probably is. To put it another way, you never get false negatives, but you can get false positives.

In this little demo filter, it's easy to get false positives:

```php
$bloom = new Bloom(40);

$bloom->add('hello');
$bloom->add('bingo');
$bloom->add('spark');

$bloom->contains('hello'); // true
$bloom->contains('bingo'); // true
$bloom->contains('spark'); // true
$bloom->contains('doggo'); // false
$bloom->contains('camel'); // true *false positive*
$bloom->contains('hydro'); // true *false positive*
$bloom->contains('plump'); // false
```

This can be improved a lot by using a hash function. The hash function jumbles up the high bits and gives a better false
positive rate.

You might have also noticed that all the inputs to the filter thus far have been exactly 5 characters long, this is no
coincidence, this is so the naive `stringToBinaryArray` implementation always returns the same length array. A fixed
length hash function, like SHA, removes this restriction because it always returns a fixed length output:

```php
private function stringToBinaryArray(string $string) {
{+    $string = substr(hash('sha256', $string), 0, 5); +}

    $binary = [];

    for ($i = 0; $i < strlen($string); $i++) {
        $binaryDigits = str_split(sprintf("%08b", ord($string[$i])));

        foreach ($binaryDigits as $binaryDigit) {
            $binary[] = (int)$binaryDigit;
        }
    }

    return $binary;
}
```

```php
$bloom = new Bloom(40);

$bloom->add('hello');
$bloom->add('bingo');
$bloom->add('spark');

$bloom->contains('hello'); // true
$bloom->contains('bingo'); // true
$bloom->contains('spark'); // true
$bloom->contains('doggo'); // false
$bloom->contains('camel'); // false
$bloom->contains('hydro'); // false
$bloom->contains('plump'); // false
```

The probability of getting false positives, or collisions, is influenced by two things, the size of the filter and the
number of items which have been added. There are formulas which will tell you the chance of getting a collision give
both those variables.


## Putting it all together

```php
class Bloom {
    private(set) array $bits = [];

    public function __construct(int $length) {
        $this->bits = array_fill(0, $length, 0);
    }

    public function add(string $string): void
    {
        foreach ($this->stringToBinaryArray($string) as $index => $bit) {
            if ($bit === 1) {
                $this->bits[$index] = 1;
            }
        }
    }

    public function contains(string $string): bool
    {
        foreach ($this->stringToBinaryArray($string) as $index => $bit) {
            if ($bit === 1 && $this->bits[$index] === 0) {
                return false;
            }
        }

        return true;
    }

    private function stringToBinaryArray(string $string) {
        $string = substr(hash('sha256', $string), 0, 5);

        $binary = [];

        for ($i = 0; $i < strlen($string); $i++) {
            $binaryDigits = str_split(sprintf("%08b", ord($string[$i])));

            foreach ($binaryDigits as $binaryDigit) {
                $binary[] = (int)$binaryDigit;
            }
        }

        return $binary;
    }

    public function __toString()
    {
        return implode('', $this->bits);
    }
}
```

