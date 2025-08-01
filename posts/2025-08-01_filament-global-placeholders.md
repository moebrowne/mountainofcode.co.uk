# Global Placeholders In Filament PHP

#Filament PHP

Recently I was adding placeholder properties to a number of form fields across a Filament PHP site and thought that
there must be a better way than copy-pasting everywhere. It occurred to me that the field's name was a great indicator
of what its placeholder should be.

Thanks to the `{php}configureUsing()` method added in v3 this was pretty easy:

```php
TextInput::configureUsing(static function (TextInput $field): void {
    match ($field->getName()) {
        'email' => $field->placeholder('john.doe@example.org'),
        'first_name' => $field->placeholder('John'),
        'last_name', 'surname' => $field->placeholder('Doe'),
        'company_name' => $field->placeholder('ACME Inc'),
        'company_url' => $field->placeholder('example.org'),
        default => null,
    };
});
```

I don't particularly like how <magic-sparkle>magic</magic-sparkle> this is; unless you know about this feature, it's
going to be hard to figure out where these values are coming from, on the other hand, they are pretty easy to search
for. What I do like is that they can be overridden at the per-field level when you get an inevitable special case:

```php
// Placeholder = john.doe@example.org
TextInput::make('email');

// Placeholder = barry.scott@example.org
TextInput::make('email')
    ->placeholder('barry.scott@example.org');
```
