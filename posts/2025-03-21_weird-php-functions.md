# Weird PHP Functions

#PHP

I wondered if there was a way to get all the internal functions in PHP. Turns out, kind of ironically, that there is a 
function for doing just that: `{php}get_defined_functions()`. For PHP 8.4 with minimal extensions that list contains
1,555 entries.

There are some functions on there that I had never come across in over 15 years of writing PHP, I'd like to highlight
some.

## `{php}_()`

Yep, just a single underscore. It is an alias of the `{php}gettext()` function which appears to be to do with localisation.
It makes sense now why Laravel uses the `{php}__()` function for localisation.

[https://www.php.net/manual/en/function.gettext.php](https://www.php.net/manual/en/function.gettext.php)

---

## `{php}chop()`

Another alias, this time for `{php}rtrim()`.

[https://www.php.net/manual/en/function.chop.php](https://www.php.net/manual/en/function.chop.php)

---


## `{php}mb_scrub()`

Replaces ill formed byte sequences with `?`

[https://www.php.net/manual/en/function.mb-scrub.php](https://www.php.net/manual/en/function.mb-scrub.php)

---


## `{php}readdir()`

Allows for reading a list of file names from a directory. 

```php
$dh = opendir($dir);

while (($file = readdir($dh)) !== false) {
    echo "filename: " . $file;
}
```

[https://www.php.net/manual/en/function.opendir.php](https://www.php.net/manual/en/function.opendir.php)

---


## `{php}count_chars()`

A weird function which given a string returns an array containing the number of times each character (byte) appears.

```php
var_export(count_chars('I am a teeeeeapot', 1));
```

```
```php
//[eval]
var_export(count_chars('I am a teeeeeapot', 1));
```​
```

The second argument is a magic number which changes the returned values. Dial 3 if you want all the unique characters:

```php
var_export(count_chars('I am a teeeeeapot', 3));
```

```
```php
//[eval]
var_export(count_chars('I am a teeeeeapot', 3));
```​
```
 

[https://www.php.net/manual/en/function.count-chars.php](https://www.php.net/manual/en/function.count-chars.php)

---


## `{php}disk_free_space()`

Want to know how much free space your disk has? PHP's got you covered.

```php
var_export(disk_free_space('/'));
```

```
```php
//[eval]
var_export(disk_free_space('/'));
```​
```

There is also the complementary `{php}disk_total_space()`

[https://www.php.net/manual/en/function.disk-free-space.php](https://www.php.net/manual/en/function.disk-free-space.php)

---


## `{php}fnmatch()`

A string matching function, similar to `{php}preg_match()` but uses pattern matching common in shells.

```php
fnmatch("backup_[0-9]*.*", 'backup_2025-01-01.gz'); // true
```

[https://www.php.net/manual/en/function.fnmatch.php](https://www.php.net/manual/en/function.fnmatch.php)

---


## `{php}frenchtojd()`

> Converts a date from the French Republican Calendar to a Julian Day Count
> These routines only convert dates in years 1 through 14 (Gregorian dates 22 September 1792 through 22 September 1806).
> This more than covers the period when the calendar was in use.

Wat?

```php
frenchtojd(month: 10, day: 2, year: 4); // 2377207
```

[https://www.php.net/manual/en/function.frenchtojd.php](https://www.php.net/manual/en/function.frenchtojd.php)

---


## `{php}get_mangled_object_vars()`

As far as I can tell this function returns an array of an objects properties and values, including protected and private
ones. Not sure what's mangled about them?

```php
class Post
{
    public string $url = 'post-about-a-thing';
    protected int $wordCount = 1435;
    private bool $isPublished = false;
}

$instance = new Post();

var_export(get_mangled_object_vars($instance));
```

```
```php
//[eval]
class Post
{
    public string $url = 'post-about-a-thing';
    protected int $wordCount = 1435;
    private bool $isPublished = false;
}

$instance = new Post();

var_export(get_mangled_object_vars($instance));
```​
```

[https://www.php.net/manual/en/function.get_mangled_object_vars.php](https://www.php.net/manual/en/function.get_mangled_object_vars.php)

---


## `{php}get_meta_tags()`

This one actually seems useful! It parses an HTML file and extracts the `{html}<link>` tags and their values.


```php
var_export(get_meta_tags('https://mountainofcode.co.uk'));
```

```
```php
//[eval]
var_export(get_meta_tags('https://mountainofcode.co.uk'));
```​
```

[https://www.php.net/manual/en/function.get-meta-tags.php](https://www.php.net/manual/en/function.get-meta-tags.php)

---


## `{php}grapheme_str_split()`

I believe this is fairly new. Used for correctly splitting a string which contains multibyte sequences, for example emoji:

```php
grapheme_str_split('emoji: 👨‍👩‍👧‍👧'); // ['e', 'm', 'o', 'j', 'i', ':', ' ', '👨‍👩‍👧‍👧']
str_split('emoji: 👨‍👩‍👧‍👧'); // Error: Unexpected encoding - UTF-8 or ASCII was expected
```

[https://www.php.net/manual/en/function.grapheme-strrpos.php](https://www.php.net/manual/en/function.grapheme-strrpos.php)

---


## `{php}hypot()`

A trigonometry shortcut. Calculates the hypotenuse given the lengths of opposite and adjacent sides of a right angle
triangle.

```php
hypot(3, 4); // 5
```

[https://www.php.net/manual/en/function.hypot.php](https://www.php.net/manual/en/function.hypot.php)

---


## `{php}phpcredits()`

The current credits for the creators/maintainers/etc for PHP.

```html
<!--[eval class="full-bleed" style="height: 400px;"]-->
<html>
<head>
    <style>
        table {
            width: 100% !important;
        }
    </style>
</head>
<body>

```php
//[eval]
phpcredits(CREDITS_ALL | CREDITS_SAPI);
```​

</body>
</html>
```

[https://www.php.net/manual/en/function.phpcredits.php](https://www.php.net/manual/en/function.phpcredits.php)

---


## `{php}str_increment()`

It's not well known, or really expected, that strings can be (in|de)cremented. The only genuine use I have found for it
is spreadsheet columns.

```php
$column = 'AA';
str_increment($column); // 'AB'
```

This works with the regular (in|de)crement syntax too. A notable difference is that the function does not modify the
variable, it returns a new value;

```php
$column = 'AA';
str_increment($column); // 'AB'
$column; // 'AA';

// vs

$column = 'AA';
++$column; // 'AB'
```

[https://www.php.net/manual/en/function.str-increment.php](https://www.php.net/manual/en/function.str-increment.php)

---


## `{php}strspn()`

This will count the longest run of characters which match a given set, starting from the beginning of the given string.

Not sure what this would be useful for.. You could see how many digits a string started with for example:

```php
strspn('000somestring12345', '0123456789'); // 3
```

[https://www.php.net/manual/en/function.strspn.php](https://www.php.net/manual/en/function.strspn.php)

---


## `{php}strpbrk()`

This will return part of the given string starting with the first occurrence of any provided characters.

```php
strpbrk('0243323-bdsadbhe-ghref', '-'); // 'bdsadbhe-ghref'
```

[https://www.php.net/manual/en/function.strpbrk.php](https://www.php.net/manual/en/function.strpbrk.php)

---


## `{php}time_sleep_until()`

Pretty obvious functionality, like sleep but with takes an absolute time rather than a relative one, like `{php}sleep()`
expects. It also supports microseconds.

[https://www.php.net/manual/en/function.time-sleep-until.php](https://www.php.net/manual/en/function.time-sleep-until.php)

---


## `{php}wordwrap()`

I'm sure I've written a user-land implementation of this function before. It will add a wrapping string every n characters.
By default, it will split on spaces, but it can split in the middle of words too. 

In the case of HTML it is always best to allow CSS to do the wrapping but could be useful in a CLI context.

```php
wordwrap(
    string: 'The quick brown fox jumped over the lazy dog.',
    width: 20,
    break: '<br>',
); // The quick brown fox<br>jumped over the lazy<br>dog
```

[https://www.php.net/manual/en/function.wordwrap.php](https://www.php.net/manual/en/function.wordwrap.php)
