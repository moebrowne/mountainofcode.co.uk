# Label Relative Dates

#date

If you ever create a UI which includes relative dates like "4 days ago", then please include the absolute timestamp.
Usually this is in a tooltip on hover. Bonus points for using a [date format suitable for humans](/human-date-formats).

There have been so many times when I've needed the absolute time something happened only to be given a
round-about-some-time-ago type of answer.

It's easy:

```php
//[eval:html]
$date = new DateTimeImmutable('2026-10-16T20:45:00+00:00');

echo <<<HTML
<time datetime="{$date->format('c')}" title="{$date->format('j M Y H:i')}">
    {$date->diff(new DateTimeImmutable())->days} days ago
</time>
HTML;
```

