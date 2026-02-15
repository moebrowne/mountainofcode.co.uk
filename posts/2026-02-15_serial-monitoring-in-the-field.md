# Serial Monitoring In The Field

#ESP32
#debugging


I have a number of ESP32-based projects which are battery+solar-powered. If anything goes wrong with them, it can be a
real pain to figure out what's wrong.

While developing, I always add helpful `Serial.print` commands in the code. They're not much help when in the field,
because the only way I have to read them is a PC. Sometimes you can move the whole thing and get the output, but I
thought there must be a better way. Especially as [Sod's law](https://en.wikipedia.org/wiki/Sod%27s_law) says that the
issue only happens in situ. 

What I wanted was a hand-held serial terminal I could plug in and see the serial output. I searched for existing
products and found [Tiny Terminal 2](http://www.technoblogy.com/show?2D8S), this had the functionality I wanted, but I
couldn't have the time and inclination to source parts, assemble it and figure out how to flash it.

Then I came across the [Serial USB Terminal](https://www.kai-morich.de/android/) Android app. This was perfect! All I
needed was a phone which I always have on me and a USB-C to USB-micro (OTG) cable.
