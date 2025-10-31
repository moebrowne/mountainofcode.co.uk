# Kagi Assistant Total Cost Extension

#browser extension
#Kagi
#AI


I've used [Kagi assistant](https://help.kagi.com/kagi/ai/assistant.html) for a while and love it; however, there is one
small thing which I found myself missing: the ability to see the total cost of a conversation, and [I wasn't alone](https://kagifeedback.org/d/8176-assistant-show-per-thread-cost/11).
I had no sense of how quickly I was using my quota, either. The data is available. Each message in a conversation has
an icon which when clicked shows the speed, token count, cost and response time. This is fine, but I want to be able to
see the cost of the whole conversation at a glance, not scroll to the end of a message, find the icon and click it.

The [October 23rd release](https://kagi.com/changelog#8716) added the total cost to the stats, but it was still hidden,
and it seemed to have some odd rounding. I wondered how hard it would be to expose this data.

I'd never made a browser extension before. For some reason I had assumed it would be a pain in the butt, and you'd have
to jump through a bunch of hoops, I was wrong. You only need two files: manifest.json and content.js, it can then be
loaded temporarily in Firefox via the debug menu here: about:debugging\#/runtime/this-firefox

The JS was simple, all that was needed was to simply iterate over all the messages in a conversation, sum all the
message costs and shove the number on the screen somewhere. There were some extra cases to cover, like updating as new
messages were added, but that was easy enough. Currently, the only limitation is that it only shows the total cost of
the messages which are visible. If the conversation branches, the hidden messages aren't counted because they are
removed from the DOM.

I also decided to expose the stats for each individual message.

The new content is highlighted in this screenshot:

![](/images/kagi-token-tally-screenshot.png)

For now, I haven't published it as an official Firefox Addon, but I'll get around to it sometime or better yet Kagi will
implement it as a first party feature.

For now if you want to install it, you can get a copy of the code from GitHub:

[<img style="max-width: 500px; margin: 0 auto;" src="https://opengraph.githubassets.com/d251596805e19d7c30dc3ff2965ccc41940d845564597fa22fb18bbac4c8e01e/moebrowne/kagi-assistant-token-tally">](https://github.com/moebrowne/kagi-assistant-token-tally)

