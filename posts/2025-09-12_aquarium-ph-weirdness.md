# 🐠 Aquarium pH Weirdness

#aquarium
#monitoring

I have a pH probe in my aquarium, ever since it was installed it's shown a drop of about 0.3 when the lights came on
and jumped back again when they went off. At first this seemed like it was working as expected, but something wasn't
right the drop always happened immediately as the lights came on.


## Lights?

In my aquarium, a pH change is expected shortly after the lights come on because it is heavily planted and 
photosynthesis affects the pH.

To prove if it was the light falling into the aquarium, I flipped the light over so that it didn't shine into the
aquarium, the lights came on, the pH dropped exactly as before.


## Interference?

Next, I turned to looking for sources of electrical interference.

I tried powering the ESP32, which was doing the data collection, from a battery rather than a mains adaptor in case it
was noisy. This had no effect.

I tried moving the PSU for the lights to the other side of the cabinet away from the pH circuitry, the effect was
immediately noticeable. Turns out that running high current DC lines right past the pH probe amplifier wasn't a good
idea, who'd of thought 🤦.

![](/images/aquarium-ph-change-1.png)

This showed a much slower and less pronounced drop as the lights came on, I had to increase the filtering because the
signal-noise ratio just jumped up.

Great, end of the story, right? Not quite because at this point that I learned that the pH should rise as the plants
photosynthesise, not fall.


## Temperature Compensation?

[I asked Claude](https://kagi.com/assistant/111e732b-b309-4d25-ab59-a42affd22a98).
It noted that some pH meters are temperature compensated and sure enough there is a <abbr title="Positive Temperature Coefficient">PTC</abbr>
thermistor present on the PCB. This didn't really fit though, the compensation amount for low temperatures is tiny and
if it was temperature based then it wouldn't be as rhythmic.

![](/images/aquarium-ph-pcb.jpg)


## What Now?

I'm going to replace the pH amplifier entirely. This [excellent article](https://www.e-tinkers.com/2019/11/measure-ph-with-a-low-cost-arduino-ph-sensor-board/)
fully reverse engineers the exact same PCB I have, and they don't have a lot of great things to say about it,
especially when it comes to the temperature compensation part.


## A Note On Calibration

Everything I have read about pH probes is that they require constant re-calibration. While I did calibrate the probe
with 3 buffer solutions, I'm aware that the values I'm getting are probably inaccurate, they are, however, probably
accurate enough. They also match with some test strips I have. I am more interested in the trend over time than the
exact value.
