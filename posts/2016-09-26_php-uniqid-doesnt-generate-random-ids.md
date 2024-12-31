# PHPs uniqid() Does Not Generate Random IDs

#php
#random is hard
#vulnerability




Recently I was involved with a security audit on a PHP based site, after i'd finished looking for XSS and SQL injection vulnerabilities I turned my attention to more subtle attack vectors.

One thing that caught my attention was the use of `uniqid` in the password reset process, this function should NEVER be used for this kind of thing...

## Unique != Unpredictable

The `uniqid` function does generate unique IDs as the name suggests but they aren't random, this is an important distinction. They are generated from a millisecond precise timestamp and if you've ever read the [top comment](http://php.net/manual/en/function.uniqid.php#95001) on php.nets documentation of `uniqid` you will know you can take a `uniqid` and turn it into a date:

```php
date("r",hexdec(substr(uniqid(),0,8)));
```

So when I noticed that `uniqid` was being used to generate the reset tokens for users accounts I knew a brutefore attack was possible. All you would need to know is the email address of an account, the exact time you sent a password reset request and the exact time you got a response back.

From these time stamps we can determine a set of `uniqid`s, one of which will be the password reset token the server generated.

## Example Exploit

If we sent a password reset request at exactly 1472397211.1198 and 430ms later the server responded we know the `uniqid` was generated in that window but we also spent 250ms connecting during which we know the token wasn't generated, so that we can add that to our start timestamp to narrow the search space.

So we can conclude that `uniqid` was called between 1472397211.3698 and 1472397211.5498, just a 180ms window, we can now procedurally generate all the possible tokens. As `uniqid` returns tokens in a hexadecimal format the easiest method is to determine the upper and lower limits then increment in hexadecimal:

```php
function uniqidGen($timestamp) {
	return sprintf("%8x%05x",floor($timestamp),($timestamp-floor($timestamp))*1000000));
}

$requestTimestamp = 1472397211.3698;
$lowerLimit = uniqidGen($requestTimestamp);	 // 57c2ff9b5a488

$responseTimestamp = 1472397211.5498;
$upperLimit = uniqidGen($responseTimestamp); // 57c2ff9b863a7
```

This gives us just 180,000 possible tokens! This is not a lot of possibilities especially if the developers of the application neglected to add any rate limiting or give the tokens an expiry date.

## What To Use Instead?

If you're using PHP 7 then your friend is `bin2hex(random_bytes(LENGTH));` where `LENGTH` is the length of the token you desire. If you are stuck with older versions of PHP then you can use `bin2hex(openssl_random_pseudo_bytes(16))`