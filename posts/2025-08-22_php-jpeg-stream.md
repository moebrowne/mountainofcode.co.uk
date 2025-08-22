# Streaming Dynamic Images With PHP 📽️

#PHP
#JPEG
#stream


I have an idea for a project, it's a visual simulation of sorts. Usually I'd turn to JS and a `{html}<canvas>` and that
would be the 'proper' thing to do, but I didn't want to do that, not this time. I wanted to write it all in PHP. I know
PHP inside out and that lets me hack projects together quickly. I also think it's fun to do something differently just
because you can. You might learn something new too.

PHP has all the tools to generate images. The issue I had was how to display the 'video' in the browser. I thought I
could generate individual images and then turn them into a video using ffmpeg, but I wanted it to be live, I didn't want
any post-processing.

I let the idea [simmer](/letting-ideas-simmer) for a bit.


## The Old Ones Are The Best

In the early 2000s I had a cheap and kinda crappy webcam, what fascinated me about it was how the web interface worked.
There wasn't any special video player, or indeed any video at all it was just an `{html}<img>` tag. The src of the image
pointed to the server which responded with a JPEG which never stopped loading...

The HTTP response looks like this:

```
HTTP/1.1 200 OK
Content-Type: multipart/x-mixed-replace; boundary=frameboundary

--frameboundary
Content-Type: image/jpeg
Content-Length: {DATA LENGTH}

{BINARY JPEG DATA}

--frameboundary
Content-Type: image/jpeg
Content-Length: {DATA LENGTH}

{BINARY JPEG DATA}

--frameboundary

...
```

This was a super simple way to stream 'frames' from a server to the browser using nothing but HTTP. It's inefficient as
hell, and I wouldn't use it in production, but for this it was perfect.

The PHP implementation was almost too easy:

```php
set_time_limit(0);

header('Content-Type: multipart/x-mixed-replace; boundary=frameboundary');

while (true) {
    echo '--frameboundary' . PHP_EOL;
    echo 'Content-Type: image/jpeg' . PHP_EOL;
    echo renderFrame() . PHP_EOL;
    
    ob_flush();
    flush();
}
```
