# Crouching Exception, Hidden Type Coercion

#PHP
#types


I like to be as strict as possible when writing PHP, it helps avoid whole classes of bugs. This is achieved in PHP by
doing two things: `{php}declare(strict_types=1)` and type declarations wherever possible.

This isn't a magic bullet. Even with 100% coverage there are type coercions and loose type checks hiding within in the
PHP engine.



### Switch Statements

```php
<?php

declare(strict_types=1);

switch (true) {
    case 1: echo 'one'; break; // This case is matched
    case true: echo 'true'; break;
}
```



### Ternaries

```php
<?php

declare(strict_types=1);

$a = 1 ? 'true' : 'false'; // $a is 'true'
$b = 0 ? 'true' : 'false'; // $b is 'false'
```

This also applies to short terneries using the `?:` syntax.



### Array Sort

```php
<?php

declare(strict_types=1);

$array = ['10', '5', 2, 1];

sort($array); // [1, 2, '5', '10']
```



### Inequalities

```php
<?php

declare(strict_types=1);

0 < true; // true
0 > true; // false
1 >= true; // true
1 <= true; // true
0 >= true; // false
0 <= true; // true
```



### Spaceship Operator

```php
<?php

declare(strict_types=1);

1 <=> true; // 0
0 <=> true; // -1
1 <=> false; // 1
0 <=> false; // 0
```



## Workarounds

To avoid all of the above, I follow some simple rules:

- Never use `{php}switch`, replace it with `{php}match`
- When using terneries use explicit strict comparison: `{php}$a = (1 === true) ? 'true' : 'false';`
- Only sort an array when you can guarantee that all the values are of the same type
- Only use inequalities when both sides are guaranteed to have the same type

If you're wondering how types can be 'guaranteed', [PHPStan](https://phpstan.org/) and code like this:

```php
class Thing
{
    private $values = [];
    
    public function addValue(int $value) {
        $this->values[] = $value;
    }
    
    public function getValues() {
        return $this->values;
    }
}

$thing = new Thing();
$thing->addValue(1);
$thing->addValue(2);

sort($thing->getValues())
```

