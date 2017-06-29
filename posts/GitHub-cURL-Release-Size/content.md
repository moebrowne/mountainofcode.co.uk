
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

- Doesn't accept HEAD requests
- Cant seem to get just headers with cURL from a GET request
- Can download a couple of bytes and inspect `Content-Range` header
- GitHub API can give you all the information in JSON format if you know the asset ID

```
curl -siLr 0-1 "https://github.com/RetroPie/RetroPie-Setup/releases/download/4.2/retropie-4.2-rpi2_rpi3.img.gz"
```
