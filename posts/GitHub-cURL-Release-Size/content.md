


<!-- more -->

- Doesn't accept HEAD requests
- Cant seem to get just headers with cURL from a GET request
- Can download a couple of bytes and inspect `Content-Range` header
- GitHub API can give you all the information in JSON format if you know the asset ID

```
curl -siLr 0-1 "https://github.com/RetroPie/RetroPie-Setup/releases/download/4.2/retropie-4.2-rpi2_rpi3.img.gz"
```
