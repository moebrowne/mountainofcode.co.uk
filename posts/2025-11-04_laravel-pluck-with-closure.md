# Laravel Plucking

#Laravel
#PHP


Laravel's pluck method recently got closure support in [PR \#56188](https://github.com/laravel/framework/pull/56188).
This is the usage example given:

```php
Country::get()
    ->pluck(fn (Country $country) => "{$country->flag} {$country->name}", 'id')
```

Before this PR to achieve the same thing, you needed something like this:

```php
Country::get()
    ->mapWithKeys(function (Country $country) {
        return [
            $city->id => "{$country->flag} - {$country->name}"
        ];
    });
```

I prefer a different approach altogether. I prefer to do a `{php}map()` and `{php}keyBy()` as separate operations:

```php
Country::get()
    ->keyBy(static fn (Country $country): int => $country->id)
    ->map(static fn (Country $country): string => "{$country->flag} - {$country->name}");
```

The primary benefit of a dual-step approach is readability. Each line has its own responsibility, one for keys, one for
values.

There are other subtle benefits too, for example accessing properties rather than magic `{php}'id'` strings or
destructured arrays means IDEs and static analysers have a much easier job helping to prevent bugs or do refactoring. It
also gives strong type guarantees for both the key and the value thanks to the closure return type declarations.



## What about performance!?!

I'm sure some people will scoff at using `{php}keyBy()` + `{php}map()` because it means that every record is looped over
twice and that's inefficient! To them I say how many rows do you have? Go count them. If it's less than a million, then
stop worrying, if it's more benchmark it.
