# RSS For Twitter Feeds

#RSS
#Nitter
#CAPTCHA

There is an almost infinite sea of posts and videos on tools, packages, updates, vulnerabilities, frameworks, ideas,
etc. out there, and I strongly believe that keeping an eye over what's new is beneficial.

I am always learning new things from stuff that floats across my feeds. Even if I never use the thing, I often pick up
ideas or ways of doing things which I can apply in a different context or a completely unrelated way.

The way I do this is primarily via RSS. Over the years, I have accrued many feeds, mostly blogs, and a few tech news
sites which overlap with my areas of interest. There are, however, a number of people who choose to share their thoughts
via 𝕏, meaning there is no RSS feed I can subscribe to.



## Nitter

Nitter is "A free and open source alternative Twitter front-end focused on privacy and performance.". One of its killer
features is that it provides an RSS feed for each account, problem solved! For a while...

𝕏 started limiting access to the public APIs which Nitter relied on, this pretty much killed the project. Only a couple
of [public instances](https://github.com/zedeus/nitter/wiki/Instances) have been kept alive. I believe they were able to
do this because they implement strict rate limiting, filtering and CAPTCHAs. This obviously blocked RSS readers.

The Nitter instance I use is [nitter.poast.org](https://nitter.poast.org), and when you first visit it shows a
'Verifying Your Browser' message for a couple of seconds and then lets you in, no 'click the checkbox' or 'select all
the pictures of road signs'. I got curious... What exactly was being verified and how?



## Reverse Engineering The CAPTCHA

I opened dev-tools and hit 'Copy as cURL', then just started taking chunks out until it stopped working. All that was
required was:

```bash
curl 'https://nitter.poast.org/xkcd/rss' \
    -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:138.0) Gecko/20100101 Firefox/138.0' \
    -H 'Cookie: res={HEXADECIMAL_VALUE}'
```

The <magic-sparkle>magic</magic-sparkle> value stored in the `res` cookie was the key. One internet search for
`"Verifying your browser" "res="` revealed [simon987/ngx_http_js_challenge_module](https://github.com/simon987/ngx_http_js_challenge_module).
This is a "Simple javascript proof-of-work based access for Nginx".

This was perfect! [Proof of work](https://en.wikipedia.org/wiki/Proof_of_work) challenges are designed to deter SPAM by
requiring each user to do a little work. For an individual, the effort is trivial, but for a bot farm the costs start
adding up.

All I needed was to extract the proof algorithm, automatically generate values for the `res` cookie, and then I could
set the cookie when making requests for the RSS feed. What I also loved about this wasn't a hack or a working-around, I
was going along with the sites' requirements.

Looking at the source of the module revealed a large blob of obfuscated JS. I dumped the source into Claude and asked it
to explain how it worked. Turns out the vast majority of the code is a JS implementation of SHA1 presumably for older
browsers, the actual algorithm is really simple. Here it is ported to PHP:

```php
function solveChallenge(string $challenge): string
{
    $i = 0;
    $byteOffset = hexdec($challenge[0]); // Get position from first character of challenge

    while (true) {
        $solution = $challenge . $i;
        $solutionHash = sha1($solution, true);

        if (ord($solutionHash[$byteOffset]) === 0xB0 && ord($solutionHash[$byteOffset + 1]) === 0x0B) {
            return $solution;
        }

        $i++;
    }
}
```

The execution time of this seems to vary wildly. It can be as high as 8 seconds or as little as 500μs, the vast majority
of the runs I did completed in ~10ms.

All that was left to do was extract the challenge value from the verification page, I choose to use a dead simple and
admittedly fragile regex: `{regex}[A-Z0-9]{40}`. It works.
