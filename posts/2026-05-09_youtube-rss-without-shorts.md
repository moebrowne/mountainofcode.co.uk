# YouTube RSS Feed Without Shorts

#YouTube
#RSS


A while ago I built a little app which allowed me to subscribe to YouTube channels using the official RSS feeds, more
about that [here](/youtube-player-rebuild). One thing that I wish it did was exclude shorts, but it seemed the only way
to do that was to use the YT API or use some hack.

Then I happened to come across [this post on HackerNews](https://news.ycombinator.com/item?id=48032508), which pointed
out that there is an undocumented URL which contains only full-length videos. It can be derived from the channel URL
with a search and replace:

Before: `https://www.youtube.com/feeds/videos.xml?channel_id=UC...`<br>
After:  `https://www.youtube.com/feeds/videos.xml?playlist_id=UULF...`

Apparently these undocumented URLs were discovered by brute force, and [there are more](https://stackoverflow.com/a/77816885).

I've updated my app to use this new feed, and it works beautifully. 


