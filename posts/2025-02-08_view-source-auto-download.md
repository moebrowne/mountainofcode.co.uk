# View Source Of Auto Downloading URLs

#source code
#browser

There have been a number of times when I have visited a URL for an RSS feed, or other text based content, only to have
the browser offer to download it for me when what I wanted was to view the textual content. Of course this isn't the
browsers fault it's obeying the [`Content-Disposition`](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Disposition#triggering_download_prompt_for_a_resource)
header.

This can easily be worked around by prefixing `view-source:` to the URL, this opens the URL in the browsers native
source viewer.

```
view-source:https://example.org/feed.rss
```
