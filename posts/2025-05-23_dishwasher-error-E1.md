# Dishwasher Error E1 🧽

#dishwasher
#debugging

I've had a small 'tabletop' dishwasher for a couple of years now, I would have a full size one, but there just isn't
space. It works, and I love it, at least it used to work...

Recently it started throwing up error code "E1" at the very start of a cycle. Reviewing the manual showed this meant:

> Longer inlet time - A flow meter, drain valve, or pump failure, it should be repaired by a qualified worker.

This wasn't super helpful so I searched online. That led me to [this video](https://www.youtube.com/watch?v=GKHm63vBIoM)
in which I learned that most of these mini dishwashers are manufactured by the same company, given a different look and 
branded.

What was really helpful was that he linked to the [service manual](https://web.archive.org/web/20250517173002/https://data2.manualslib.com/pdf7/193/19260/1925921-toshiba/dws22a_series.pdf?151fc027154ebe090b27194304328148).


## Service Manual

![Dishwasher Exploded Diagram](/images/dishwasher-exploded.png)

The service manual included all the details, from circuit diagrams to part lists and some pretty cool exploded diagrams,
which reminded me of the 'how it works' books I had as a kid. It also expanded on the meaning of the "E1" error code:

> During the water inlet step, if the flow meter can't detect the defined water after 4 Minutes, or can’t detect 30
> pulse after 60s, the dishwasher will warning for E1.

Something was up with the flow meter...

I took the back off revealing the flow meter, nothing was obviously wrong, no loose cables, but it was pretty hard to
see. While pondering if I had the time to take it completely apart before dinner, it occurred to me that a flowmeter is
a moving part and the whole water tank had become white with limescale. Moving parts and limescale don't mix well, I
theorised that the impeller in the meter had become caked in limescale.

I added a warm citric acid solution into the empty tank and let it sit for 10m. I then forced it to drain the tank (hold
Hygiene and Rapid for 3s), which would flush the solution through the meter.

I needed to do this twice because after one successful cycle it gave the same error again, but it's now happily saving
me from having to hand-wash the dishes again.

