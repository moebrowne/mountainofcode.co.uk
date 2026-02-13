# Passive WiFi Detector 💡

#WiFi


Ever wanted to 'see' WiFi? With just an LED and a diode you can!

When I read [this blog post](https://siliconjunction.wordpress.com/2025/12/12/a-beginners-two-component-crystal-style-wi-fi-detector/)
I had to build one. I already had some red LEDs, but I had to buy the special diodes. When they arrived, I quickly
soldered them together, making sure the white band on the diode is opposite the flat on the LED.

![](/images/wifi-led.jpg)

It worked really well. It's a very short range detector and will completely stop working if it's touching bare skin.

Orientation matters, It's effectively a dipole antenna. The length of the legs also matters, ideally each leg should be
length a quarter wavelength AKA 31&nbsp;mm. LED legs are different lengths, so they were removed, the diode legs were
28&nbsp;mm, which was close enough.

After playing with it for a while, I wondered if the missing 3&nbsp;mm was making a difference. I soldered on a bit of extra
wire. I'm not sure if it made a difference, maybe it was a bit brighter.

Attaching it to the antenna of my router showed constant background traffic.

<video width="700" height="700" controls>
    <source src="/images/wifi-led.webm" type="video/webm">
</video>

I also tested it around my microwave while it was running and found no signs of leakage, I didn't really expect to
because it's fairly modern.

An idea I had when I first read the original articles was to make 8 of them and arrange them in a circle or star pattern
so that the polarisation could be observed. Now that I can see how close the detector needs to be, I don't think it will
work. Maybe I'll re-visit the idea in the future.

