title: "A Super Capacitor Powered Raspberry Pi"
tags:
- Raspberry Pi
- Super Capacitors
author: Oliver
photos:
- /images/RPi-Super-Cap-PSU.jpg
---

Super capacitors are awesome, cheap, easily obtainable and can be a little dangerous. They have a massive energy density and are willing to give up their energy very VERY quickly.

You don't have to discharge all this energy all at once, as fun as sublimating copper and throwing sparks is, they can be discharged at any rate, meaning you could connect it up to an LED to power it forever more, or for example a Pi for a good while.

So I gave it a go and it worked!

<!-- more -->

Before I go any further I wanted to say this isn't an original idea. Credit for that goes to Paul Granjon, see his [blog post](http://www.zprod.org/zwp/making/supercapacitor-raspberry-pi/). But I wanted to give it a go myself and Paul used a voltage regulator rather which can be quite energy wasteful.

## The Capacitors

There are a range of super caps in varying sizes available on eBay, so long as your willing to wait for shipping from China...

I settled for 6 500F (500,000,000 microfarad) super capacitors off eBay for about £15, I was bidding on some 8000F (yes 8KF!!) caps but they went outside my budget.

The capacitors have a maximum rating of 2.7v each which isn't useful for much but when caps are connected in series their voltages are combined, meaning that my 6 caps connected in series have an absolute max rating of 16.2v, far more useful.

## How Do You Power A Raspberry Pi Off 16v?!

Any one who knows even a little about the Raspberry Pi or USB knows USB runs off 5v not 16v, this is where the step down converter (also known as a buck converter or variable switch mode power supply) comes in, this is a little circuit that takes a variable voltage and outputs a lower voltage.

In this case I can put in 16v and get out the 5v the Pi wants, perfect. It also has the added benefit that as the voltage on the capacitor bank drops the step down converter maintains the 5v output and will keep doing so until the caps drop below about 7v ish.

These converters aren't hard to come across and you can get them easily off the internet, but often they will have a fixed output, that is they will only output one voltage so be sure to get one that outputs 5v.

I opted for [this](http://www.ebay.co.uk/itm/252261788525) kit off eBay, it was only the components and a layout but that's all I needed and wanted as i'll take any excuse to put something together myself. It turned out rather well and all the magic smoke stayed in when I powered it on!

I also added a 7 segment display that showed the voltage currently being output and wired in a jumper so I could switch between showing the output voltage and the voltage on the bank. Stuff like that is another reason I like to get the components rather than buying something ready made.

## So How Long Will It Power A Pi For?

The short answer is, well not that long...

I connected up the caps to the buck converter and the converter to the Pi via a little USB volt/current/time meter i use to monitor whats going on and booted the Pi just leaving it completely idle.

I took readings of the voltage on the cap bank, the voltage out of the converter and the total power consumed by the Pi in mAh every minute.

The graph below says it all, it ran for little more than 13 minutes. After only about 10 minutes the buck converters output voltage started to drop despite by efforts to adjust it to compensate, I expected the voltage to drop as the converter has a voltage sink of about 2v but was hoping it wouldn't be quite so soon. As the voltage approached 4v the Pi became unstable, restarting so I unplugged it all.

![Super Capacitor Powered Raspberry Pi Discharge Graph](/images/RPi-Super-Cap-Discharge-Graph.jpg)

I could get more out of the caps as they weren't charged to their maximum 16v to start with but I would only get a little more and the Pi was idle.. I planned to run it again running something like `stress` running, with a wifi adapter connected or with heavy ethernet activity but I think we know what the outcome is going to be...


# Notes
+ Super capacitors are awesome
  + cheap
  + massive energy density
+ Step Down / Buck Converter
  + 2.7v caps, in series 15v, 5v wanted
  + what happens when voltage on bank drops too low
- mobile Pi
- how long lasts
  - idle
  - busy
  - busy with wifi
- needs case
  - for caps
  - for step down
