# Iron Bloom - Organic Map Generation

#project
#devlog


The very first iteration of the map generator was as basic as it gets. Generate a random width/height, fill with floors
tiles, cover the perimeter in walls, select random floor tiles for the player and a chest. It worked, but it obviously
didn't make for engaging game play.

![](/images/iron-bloom-square-rooms.png)

I wanted the room generation to be deterministic but organic, rooms, corridors, nooks, dead ends, etc. Some research
later turned up the [Random Walk algorithm](https://en.wikipedia.org/wiki/Random_walk). This probably should've been
obvious to me, because I'd used it on my [visual hash project](/wanderer).

It's a deceptively simple algorithm:

1. Place a floor tile
2. Take a step in a random direction
3. goto 1

Run this a couple of hundred times, ignore all the times it 'walked' back on itself, and you end up with a surprisingly
natural result.

![](/images/iron-bloom-drunken-rooms.png)

The nature of the algorithm is such that every tile is guaranteed to be reachable from every other tile.

It did create a challenge when placing doors. Picking a random wall was no longer even close to good enough. The number
of corners almost guaranteed that a door would be placed in an inaccessible location.

![](/images/iron-bloom-nonsense-doors.png)

The only places a door makes sense are walls which have floor and void neighbouring tiles (north, south, east and west).
This was easily achieved by filtering all wall tiles for these conditions.



## Iterative Generation

Initially the idea was that the player could only see the current room. Moving to another room would hide the previous
one. This side stepped the need to make rooms to 'fit' together when navigating, but this was no longer necessary. Each
room could easily connect to the next.

A condition in the wandering algorithm was all that was needed:

1. Select a random direction to move
2. If the destination tile is not empty: goto 1
3. Take a step and place a floor tile
4. goto 1

Now walking through a door reveals a whole new piece of the map alongside the current one. This does mean that the map
could grow infinitely, but I have some ideas to limit that.

<video controls><source src="/images/iron-bloom-room-gen.webm" type="video/webm"></video>



## Biases

While testing the generation, I noticed that it felt a bit cramped. Each new room was almost always a blob, very few
corridors and little distance covered. I added a bias to the direction selection to slightly favour directions which
headed away from the maps' origin. This helped a lot.

