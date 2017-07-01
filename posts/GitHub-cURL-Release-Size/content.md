
For my [Raspberry Pi Image Manager](https://github.com/moebrowne/RPi-image-manager) project I wanted 
 to show a progress bar as an image is downloaded. This was easy as a simple `HEAD` request would 
 include in the response a `Content-Length` header telling me the size and allowing progress to be 
 calculated.

This worked until I wanted to include the RetroPie image. The RetroPie devs store their images on
 GitHub, not a problem you might think, business as normal. GitHub however don't allow `HEAD` 
 requests to downloads, no idea why. So now I had no way of getting the image size without downloading
 the whole image.

At least not without some hackery...

<!-- more -->

## Partial Requests To The Rescue

While we don't want to get all the data, a `GET` request does contain the `Content-Length` header
 we're after. Fortunately we don't have to get all the data, we can make a partial request. Using the
 `Content-Range` header we can instruct GitHub to send us only a small amount of the data but all the
 headers.

## Show Me Some Code!

```
curl -L -i -r 0-1 https://github.com/RetroPie/RetroPie-Setup/releases/download/4.2/retropie-4.2-rpi2_rpi3.img.gz
```

The important part is `-r 0-1`. This causes cURL to send a `Content-Range` header with the value
 `0-1` which means "get me the first byte only". Now we have easy access to the `Content-Length`
 header and we only have to download an additional byte.

## Isn't There An API For This?

Yes. GitHub does expose an API that allows you to fetch a JSON object that represents everything you
 could want to know about repo downloads but I wanted something simple and easy to access and that 
 didn't require decoding a JSON object.
