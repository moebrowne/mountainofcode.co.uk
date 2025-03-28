# PHP + cURL = Super Fast Parallel Requests

#php
#cURL

I have been re-building my [YouTube video aggregator](https://github.com/moebrowne/YouTube-Subscription-List) recently.
One of the things I wanted to improve was the feed loading time. The original version used [`{php}curl_multi_init()`](https://www.php.net/manual/en/function.curl-multi-init.php)
which allowed many requests to be processed in parallel. It took 11s to load 100 feeds, it was acceptable but left a
lot to be desired.

The first attempt looked like this:

```php
$curlHandles = [];

$multiHandle = curl_multi_init();
curl_multi_setopt($multiHandle, CURLMOPT_MAX_HOST_CONNECTIONS, 20);

$shareHandle = curl_share_init();
curl_share_setopt($shareHandle, CURLSHOPT_SHARE, CURL_LOCK_DATA_DNS);
curl_share_setopt($shareHandle, CURLSHOPT_SHARE, CURL_LOCK_DATA_SSL_SESSION);
curl_share_setopt($shareHandle, CURLSHOPT_SHARE, CURL_LOCK_DATA_CONNECT);

foreach ($urls as $channelId => $url) {
    $curlHandle = curl_init();

    curl_setopt_array($curlHandle, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SHARE => $shareHandle,
        CURLOPT_USERAGENT => 'RSS Feed Reader/1.0',
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_ENCODING => 'gzip',
    ]);

    curl_multi_add_handle($multiHandle, $curlHandle);

    $curlHandles[$channelId] = $curlHandle;
}

do {
    $mrc = curl_multi_exec($multiHandle, $pendingRequests);

    if (curl_multi_select($multiHandle, 0.1) === -1) {
        usleep(5_000);
    }
} while ($pendingRequests > 0 && $mrc === CURLM_OK);

foreach ($curlHandles as $channelId => $handle) {
    $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    $content = curl_multi_getcontent($handle);

    if ($httpCode === 200) {
        // process $content...
    }

    curl_multi_remove_handle($multiHandle, $handle);
    curl_close($handle);
}

curl_multi_close($multiHandle);
curl_share_close($shareHandle);
```

This was an immediate improvement on the original, loading all the same feeds in 1.8s. Waay better. The main difference
is the use of [`{php}curl_share_init()`](https://www.php.net/manual/en/function.curl-share-init.php), this allows the
DNS and TLS setup to be shared across all requests.


## Even Faster?

While this was both good enough and much faster than before I wondered if it could be faster. Requesting a single feed
in FireFox completed in ~100ms, this was the lower bound minimum time. This means there is 1,700ms of room for
improvement, what was the bottleneck?

My first thought was the connection limit, more connections more better right? How about 100? The thinking being that
each request would effectively have a dedicated connection. It was slower, a lot slower, over 5s slower. I suspected
that I had reached a connection limit imposed by YouTube and the requests were being queued, so I lowered the limit
until I found the sweet spot:

- 100 => >5,000ms
- 20 => 1,800ms
- 10 => 800ms
- 5 => 480ms
- 2 => 390ms
- 1 => 300ms

At first this didn't make sense, why would fewer connections improve performance of parallel requests? Then it dawned on
me, I was using HTTP2.0 (`{php}CURL_HTTP_VERSION_2_0`) which can multiplex many requests over a single TCP connection.
Increasing the number of connections was creating whole new HTTP sessions, each one subject to TCP slow start. Making
many parallel requests efficiently is the whole reason HTTP2 was invented.

This was easily confirmed, I changed `{php}CURL_HTTP_VERSION_2_0` to `{php}CURL_HTTP_VERSION_1_1` and the response time
tanked. I also noted that while using HTTP1.1 adding more connections did improve performance to a point.


## MOAR Faster! 🏎️

It's possible there is still a little more room for improvement, namely using [`{php}curl_share_init_persistent()`](https://www.php.net/curl-share-init-persistent).
This un-released feature allows connections to be re-used between PHP invocations. I reckon this could potentially save
the cost of the TLS and TCP connection or about 70ms.

I will give it a go when PHP8.5 RC1 is available.
