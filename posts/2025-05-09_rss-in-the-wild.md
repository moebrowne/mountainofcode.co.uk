# RSS In The Wild

#HTTP
#RSS
#small web

I recently came across [Kagi Small Web](https://kagi.com/smallweb) after I started using [Kagi](https://kagi.com/) as my primary
search engine. From their launch [blog post](https://blog.kagi.com/small-web):

> “small web” typically refers to the non-commercial part of the web, crafted by individuals to express themselves or 
> share knowledge without seeking any financial gain. This concept often evokes nostalgia for the early, less 
> commercialized days of the web, before the ad-supported business model took over the internet

To be included on the list you have to meet certain criteria, one of which is to have an RSS/Atom feed of the content.
When I created the RSS feed for this site I searched for the best practice, RSS vs Atom, which content-type header to
use, etc, etc.

So when I discovered that the list of sites is available on GitHub at [kagisearch/smallweb](https://github.com/kagisearch/smallweb/)
I wondered what conclusions everyone else came to...



## Scraping

I wanted to scrape both the HTTP headers and body for all the feed URLs. I threw together a quick bash script which ran
the curl requests in parallel.

I chose not to follow any redirects, forced all URLs to use HTTPS and set a hard timeout of 3 seconds.

Of the 14,513 URLs 12,929 returned HTTP 200, all other responses were discarded. The data was then passed through some
gnarly grep one-liners to produce the graphs below.

A total of 3,024 MB was downloaded.



## RSS vs Atom

```php
//[eval]
echo new \Pierresh\Simca\Charts\BarChart(700, 300)
    ->setSeries([[9029, 4019]])
    ->setLabels(['RSS', 'Atom'])
    ->render();
```

<details>
<summary>Show code</summary>

```bash
grep --no-filename -EiRo '<(rss|feed)' bodies/ | sort | uniq -c | sort -rn
```

</details>



## Content-Type Header

There are many different content types which can be declared for a feed, which is most common?

```php
//[eval]
echo new \Pierresh\Simca\Charts\BarChart(700, 600)
    ->setSeries([[4375, 837, 255, 223, 24, 20, 19, 3, 2, 1]])
    ->setOptions([
        'labelAngle' => 45,
    ])
    ->setLabels([
        'application/xml',
        'text/xml', 
        'application/rss+xml',
        'application/atom+xml',
        'application/octet-stream',
        'text/html',
        'application/x-rss+xml',
        'text/plain',
        'application/rdf+xml',
        'binary/octet-stream',
    ])
    ->render();
```

<details>
<summary>Show code</summary>

```bash
grep --no-filename -EizR "http[0-9\/\.]+ 200" headers | tr '\0' '\n' | grep --no-filename -Eio '^Content-Type:\s[^;]+$' | tr '[:upper:]' '[:lower:]' | sort | uniq -c | sort -nr | head -n 10
```

</details>



## Charset

Not all sites included a charset for the feed, but when they did what was is it set to?

```php
//[eval]
echo new \Pierresh\Simca\Charts\BarChart(700, 400)
    ->setSeries([[7146, 4, 4, 1]])
    ->setLabels(['utf-8', '"utf-8"', 'iso-8859-1', 'utf8'])
    ->render();
```

<details>
<summary>Show code</summary>

```bash
grep --no-filename -EizR "http[0-9\/\.]+ 200" headers | tr '\0' '\n' | grep -Eio '^Content-Type:.+$' | grep -Eio 'charset=[^;]+$' | tr '[:upper:]' '[:lower:]' | sort | uniq -c | sort -nr | head -n 10
```

</details>



## Path

What is the path to the feed?

Trailing slashes were stripped before aggregation.

```php
//[eval]
echo new \Pierresh\Simca\Charts\BarChart(700, 400)
    ->setSeries([ [4533, 2199, 1296, 929, 914, 765, 669, 144, 140, 126, 101] ])
    ->setLabels([
        '/feed',
        '/feed.xml',
        '/index.xml',
        '/rss.xml',
        '/feeds/posts/default',
        '/rss',
        '/atom.xml',
        '/feed.rss',
        '/blog/feed',
        '/feeds/all.atom.xml',
        '/blog/feed.xml',
    ])
    ->setOptions([
        'labelAngle' => 45,
    ])
    ->render();
```

<details>
<summary>Show code</summary>

```bash
grep -Eoi '\.[a-z]+/.+' smallweb.txt | grep -Eio '/.+' | sed 's/\/$//' | sort | uniq -c | sort -rn | head -n 10
```

</details>



## gTLD Domain Choice

```php
//[eval]
echo new \Pierresh\Simca\Charts\BarChart(700, 400)
    ->setSeries([[8136, 930, 787, 694, 511, 403, 241, 213, 140, 130]])
    ->setLabels(['.com', '.net', '.org', '.io', '.dev', '.me', '.blog', '.co.uk', '.de', '.xyz'])
    ->render();
```

<details>
<summary>Show code</summary>

```bash
grep -Eoi '\.[^/]{2,7}/' smallweb.txt | sed 's/\/$//' | sort | uniq -c | sort -rn | head -n 10
```

</details>



## Web Server

```php
//[eval]
echo new \Pierresh\Simca\Charts\BarChart(700, 400)
    ->setSeries([[2585.0, 2500, 1599, 1244, 1008, 872, 380, 363, 326, 325]])
    ->setLabels(['nginx', 'cloudflare', 'github.com', 'apache', 'netlify', 'blogger-renderd', 'caddy', 'esf', 'vercel', 'openresty'])
    ->setOptions([
        'labelAngle' => 45,
    ])
    ->render();
```

<details>
<summary>Show code</summary>

```bash
grep --no-filename -EiRo '^server:.+$' headers/ | tr '[:upper:]' '[:lower:]' | sort | uniq -c | sort -nr | head -n 10
```

</details>



## Categories

I parsed out all the `{html}<category term=""/>` nodes from the RSS feeds.

The number of categories per feed, case-insensitive.

```php
//[eval]
echo new \Pierresh\Simca\Charts\BarChart(700, 400)
    ->setSeries([[209, 109, 82, 62, 46, 40, 32, 30, 20, 21, 24, 32, 7, 11, 18,]])
    ->setLabels(['1-10', '11-20', '21-30', '31-40', '41-50', '51-60', '61-70', '71-80', '81-90', '91-100', '101-110', '111-120', '121-130', '131-140', '141-150'])
    ->setOptions([
        'labelAngle' => 45,
    ])
    ->render();
```


Most common categories across all feeds. Case-insensitive, each category was counted only once per feed.

```php
//[eval]
echo new \Pierresh\Simca\Charts\BarChart(700, 400)
    ->setSeries([[193, 193, 191, 179, 178, 163, 156, 149, 147, 146, 132, 124, 123, 122, 119]])
    ->setLabels([0 => 'python', 'books', 'music', 'linux', 'politics', 'history', 'programming', 'science', 'security', 'art', 'video', 'education', 'technology', 'ai', 'writing'])
    ->setOptions([
        'labelAngle' => 45,
    ])
    ->render();
```
