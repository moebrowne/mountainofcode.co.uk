# LED Parasites

#LED
#low power tech

I've built a few solar-powered projects over the last couple of years, primarily remote sensing, an ESP 32, battery and
solar panel.

All the projects have used a small DC-DC converter to lower the voltage of the PV. The trouble with these boards is
that they always seem to come with a bright blue power LED. Initially, I didn't think anything of it, they are tiny and
must use a small amount of energy, right?

![](/images/led-parasite.jpg)

The trouble is that they consume more energy than you might think, and in a low power project every little bit counts.
Say an LED consumes 5 mA, over 24hrs that is 120 mAh, given a 1000 mAh battery that's 10%. That's not efficient.

The solution is to just de-solder it; I've not come across any board which has a problem with this.
