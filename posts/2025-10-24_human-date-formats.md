# 🕰️ 'j M Y' For People, ISO8601 For Machines

#date

People should never need to be concerned with date formats; they shouldn't even be aware that a date has been
'formatted'. They should be able to scan some content which contains a date, and not have to stop to parse it, it should
be immediately obvious.

There is no need to use easy-to-mistake formats like 2025-09-01 and 01-09-2025, developers know this is the 1st of
September, but developers aren't the ones using your app. It's slow to read, and a non-zero number of those people will
see that date as the 9th of Jan.

If you are presenting a date for human eyeballs use `{php}date('j M Y')` aka 
```php
//[eval]
echo '\'' . date('j M Y') . '\'';
```
. It's short, quick for the brain to parse, intuitive and unambiguous.

Anytime dates are stored use machine-readable formats like ISO8601 
(```php
//[eval]
echo date('c');
```)
or a unix timestamp 
(```php
//[eval]
echo date('U');
```)
