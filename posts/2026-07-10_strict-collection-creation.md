# Strict Collection Creation

#PHP
#collections


I like to write strictly typed PHP, or at least as much as possible. Primarily this boils down to using [PHPStan](https://phpstan.org/).

One aspect where I find this challenging is [Laravel Collections](https://laravel.com/docs/12.x/collections).
Specifically creating single-type Collections. For example, a `WidgetCollection` should guarantee that the
collection only ever contains instances of `Widget`:

```php
/** @extends \Illuminate\Support\Collection<Widget> */
class WidgetCollection extends \Illuminate\Support\Collection
{

}

class Widget
{
    public __constructor(
        public string $name,
    )
}
```

The issue I have is that it's awkward to populate. For example, if I want to create a Collection of widgets given an
array of names, then this would seem obvious:

```php
$widgetNames = [
    'image',
    'text',
    'graph',
];

WidgetCollection::make($widgetNames)
    ->map(static fn (string $name): Widget => new Widget($name));
```

The problem is that `WidgetCollection::make($widgetNames)` creates a Collection of strings, this breaks the guarantee.

There are a number of different options, none of them perfect.



## foreach

```php
$widgets = WidgetCollection::empty();

foreach ($widgetNames as $widgetName) {
    $widgets->push(new Widget($widgetName));
}
```



## times

```php
WidgetCollection::times(
    count($widgetNames),
    static fn (int $index): Widget => new Widget($widgetNames[$index])
);
```



## array_map

```php
WidgetCollection::make(
    array_map(
        static fn (string $name): Widget => new Widget($name),
        $widgetNames,
    )
);
```



## Dual Collections

```php
WidgetCollection::make(
    Collection::make($widgetNames)
        ->map(static fn (string $name): Widget => new Widget($name))
);
```

or

```php
Collection::make($widgetNames)
    ->reduce(
        static function (WidgetCollection $collection, string $name): WidgetCollection {
            return $collection->push($name);
        },
        new WidgetCollection(),
    )
```



## Abstraction

```php
/** @extends \Illuminate\Support\Collection<Widget> */
class WidgetCollection extends \Illuminate\Support\Collection
{
    public function fromArrayOfNames(array $names): self {
        // ... pick any of the above and not worry about it again
    }
}
```


