# ESP32 Firmware Corruption?

#ESP32


I built a simple Wi-Fi thermometer to monitor the temperature of a [Hotbin](https://www.hotbincomposting.com/). It uses
and ESP32 and sends the data to Prometheus for storage. It's not uncommon for there to be gaps in the data because it is
entirely solar-powered. Especially so in the winter months. 

Recently there was an unusually long gap. Over a week went by with no data. That included some days which I was sure had
enough sun to wake it up. I plugged it into [a serial monitor](/serial-monitoring-in-the-field) to see what was going on.
I always leave useful `Serial.print()` calls in my code for exactly this kind of thing. It showed:

```
Connecting to WiFi.......... FAILED!
```

I assumed interference was to blame. I'd recently had to change my Wi-Fi channel because my neighbour had set up a new
AP on the same channel as me. I moved the ESP right next to my router... Same failure.

This was really weird. It had worked perfectly for nearly 3 years.

I wondered if there had been a random bit flip or some kind of firmware corruption, but I would expect that to have
caused a fatal error, rather than a Wi-Fi timeout. I wanted to dump the firmware to compare it to the [original](https://git.mountainofcode.co.uk/bin-o-meter),
but I couldn't find any way of recovering a readable firmware dump.

Reflashing the firmware fixed whatever the issue was 🤷

