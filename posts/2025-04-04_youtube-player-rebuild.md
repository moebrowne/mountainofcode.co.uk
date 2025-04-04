# 📺 YouTube Subscription List

#YouTube
#project
#rebuild
#RSS

I have subscribed to various YouTube channels over the years and originally had a YouTube account to track them all.
After I became increasingly aware of privacy and tracking on the internet I wanted to find a way to still be able to
consume the same content without needing to log in.

Being a web developer I obviously built my own web app. It was a relatively simple app, especially as YouTube amazingly
provides an RSS feed for each channel. I could parse the RSS feeds of all the channels I was interested in, aggregate
all the videos together into a single grid, pull in thumbnails. Easy.

![Screenshot of video grid](/images/youtube-subscriptions-list.png)

The original app was written in 2015(!), and I've used it pretty much every day since. I've added a few features over
the years like featured channels, whose thumbnails show up at x2 size, and keeping track of which videos I've watched.
There were a couple of annoying things about it:

1. I had to manually run the server
2. Some thumbnails would show the default placeholder
3. The feeds had to be manually refreshed via a terminal

After reviewing the decisions 10-year-ago me had made, I decided to rebuild it from scratch and also because that's a
web developers favourite thing to do 😆. I started by putting together a list of what I _needed_ it to do and also what
I _wanted_ it to do. Doing this helps me focus on what is important to make a thing actually usable.

**Requirements**

- Pull RSS feeds for channels
- List videos in a grid - with thumbnail and title
- Order by date
- Have 'featured' channels which show a 2x thumbnail
- Save watched videos to LocalStorage
- Watching a video opens in a dialog which takes up the full viewport
- Video feeds are refreshed in the background - cron?
- Refresh the feeds in parallel
- Cache the feeds where possible
- Lazy load thumbnails - no layout shift
- Simple - no dependencies


**Side quests**

- Set it up as a docker service which runs on boot
- Auto update the subscription list when new videos are loaded - <abbr title="Server Sent Events">SSE</abbr>? polling?
- Only refresh feeds when I'm at the computer - use [lock watch](https://github.com/moebrowne/lock-watch)?
- Handle different thumbnail formats
- Management of subscriptions - list/add/remove/feature
- Search
- Shared cURL resources - `curl_share_init` and the new RFC [`curl_share_init_persistent`](https://www.php.net/curl-share-init-persistent)


Over the course of 3 evenings I (aka me and [Claude](https://en.wikipedia.org/wiki/Claude_(language_model))) wrote the
whole thing from the ground up. I also managed to achieve some of the side quests or realise that I didn't need them.


## Refreshing Feeds

The core of the app is the feed refresh, see [this post](/php-curl-parallel) for details, but the short version is that
it is really fast, 600 feeds per second fast.

I realised, while thinking about how best to process updates in the background, that the requirement to update in the
background had gone, it's so fast that it can happen every page load. Part of me wanted to be able to keep it working
offline, but it's a video player, if you're offline the whole thing is useless.


## Thumbnails

YouTube thumbnails are annoying... I don't mean clickbait, that is annoying too, I mean that to get the right thumbnail
you have to blindly request the different versions and then determine if a placeholder image was returned based on the
filesize. What would've been so hard about returning a 404 status code?

For the thumbnails in the app I created an endpoint which abstracts this nonsense and allows them to be cached easily.


## OSS

Of course the whole thing is open source if you want to run your own version:

[![](https://opengraph.githubassets.com/ed5048a93d41486dad008606962a44c66a835d116e938735e6ff826374e8f3a8/moebrowne/YouTube-Subscription-List)](https://github.com/moebrowne/YouTube-Subscription-List)
