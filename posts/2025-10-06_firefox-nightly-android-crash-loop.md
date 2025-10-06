# Firefox For Android Crash Loop

#Firefox
#Android

I run Firefox nightly on both my desktop and my phone, I like to live on the edge. Recently the Android version bit me.

Initially opening Firefox was fine, my last tab loaded like normal, and I could browse around, but going to the new-tab
screen caused it to crash. Usually when these kinds of bugs happen, there's an update the same day and everything gets
fixed. Not this time.

Three or four updates came and went over 2 days, this wasn't going to magically fix itself. Things had actually become
worse, now Firefox would crash loop because it was showing the new tab screen on boot… It was now unusable… I tried all
the usual things, from [restarting everything](https://www.youtube.com/watch?v=5UT8RkSmN4k), clearing caches, etc. I
really didn't want to clear the app's storage because that would clear all my bookmarks, history, sessions, etc.

Then I had an idea 💡 When Firefox opened, there was a brief window where the IU was visible, I thought maybe during
that window I'd be able to open the settings and, I don't know, disable extensions or something.

I opened Firefox and spammed the tab list button, the list showed briefly, and then it crashed.

Next I tried the same thing with the URL bar, same thing, keyboard briefly pops up, and then it crashed.

I tried the last button on that UI, the incognito button in the top right. Spammed the hell out of it as Firefox opened,
and then it crashed… Except this time when I ran Firefox after it crashed, everything started working again, something
about switching that UI had fixed it.
