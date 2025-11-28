# Degoogling My Firemap Project 🚒

#degoogling
#maps

Quite a while ago I discovered that the [Dorset & Wiltshire fire and rescue website](https://www.dwfire.org.uk/) had a
feed of the latest incidents. What peaked my interest was that they included the GPS coordinates of the incident. I
wanted to map these over time and create a heat map.

It was a hack project, so I took the path of least resistance and dropped in Google Maps. I wasn't a fan of having to
create a Google Cloud Console account to get an API key to do this, but I wanted to get it working ASAP.

![firemap-heatmap.png](/images/firemap-heatmap.png)

Fast-forward more than 8 years and my dislike of all things Google has grown and so has the choice and maturity of
alternative interactive maps.

I choose [Leaflet](https://leafletjs.com/) as I've used it a couple of times before. Initially I thought that I would
have to jump through some hoops to get the heatmap working, turns out it was just as simple as before. The hardest part
was tweaking the colours to my taste.

[![](https://opengraph.githubassets.com/284df458788c0e4eb416dd42ec7ea896536ed043cfd502a732153316462649a0/moebrowne/firemap.mountainofcode.co.uk)](https://github.com/moebrowne/firemap.mountainofcode.co.uk)

While I was there I also did a bunch of quality-of-life updates to the whole project.

One less Google dependency in my projects.
