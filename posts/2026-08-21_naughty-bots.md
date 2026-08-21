# Bad Bots Hall Of Shame 🤖

#robots.txt
#honeypot
#bots

I wanted to set up a honey pot for bots which intentionally crawl links which are disallowed in the `robots.txt` file.
I have no idea if there are any, I bet there are.

The implementation is simple, add `Disallow: /bad-bots` to the `/robots.txt` file and log the user agents which visit.
Additionally, I have included a visually hidden link to the honey pot with `{html}rel="nofollow"`

Now we wait...

<a href="/bad-bot" rel="nofollow" style="display: none;"></a>

## Hall Of Shame

What did we catch?

```
```php
//[eval]
$path = __DIR__ . '/../bad-bots';
touch($path);

$handle = fopen($path, 'r');

$badBotCount = [];
while($ua = fgets($handle)) {
    if (trim($ua) === '') {continue;}
    if (array_key_exists($ua, $badBotCount) === false) {
        $badBotCount[$ua] = 0;
    }
    
    $badBotCount[$ua]++;
}

arsort($badBotCount, SORT_NUMERIC);

foreach ($badBotCount as $ua => $count) {
    echo str_pad($count, 5) . $ua;
}

if ($badBotCount === []) {
    echo 'No bad bots yet';
}
```
```
