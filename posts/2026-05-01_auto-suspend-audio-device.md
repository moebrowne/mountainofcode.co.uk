# Bluetooth Headphones Battery Drain 🎧

#audio
#bluetooth


Do your bluetooth headphone batteries not last as long as they used to? It might be that they never sleep.

With my headphones if the PC is outputting an audio stream, even if that stream is silence, then they won't sleep and
quickly drain the battery. On Linux you can run `pactl list sinks short`, if it shows your device as `RUNNING` but
nothing is playing then there are rouge silent streams running.

Thankfully there is an easy way to see which processes they're coming from, `pactl list sink-inputs`:

```
Sink Input #9
	Driver: protocol-native.c
	Owner Module: 10
	Client: 11
	Sink: 24
	Sample Specification: s16le 1ch 44100Hz
	Channel Map: mono
	Format: pcm, format.sample_format = "\"s16le\""  format.rate = "44100"  format.channels = "1"  format.channel_map = "\"mono\""
	Corked: no
	Mute: no
	Volume: mono: 65536 / 100% / 0.00 dB
	        balance 0.00
	Buffer Latency: 174 usec
	Sink Latency: 40092 usec
	Resample method: speex-float-1
	Properties:
		media.name = "playback"
		application.name = "speech-dispatcher-dummy"
		native-protocol.peer = "UNIX socket client"
		native-protocol.version = "35"
		application.process.id = "42146"
		application.process.user = "bob"
		application.process.host = "my-pc"
		application.process.binary = "sd_dummy"
		application.language = "C"
		window.x11.display = ":1"
		application.process.machine_id = "a9c4bf5f26fc4e1f89073d070a7ddadf"
		module-stream-restore.id = "sink-input-by-application-name:speech-dispatcher-dummy"
```

In this case it's GNOME screen reader to blame, but I have seen Firefox, VirtualBox and many others endlessly outputting
silence.

One option is to kill the process, often times that is overkill. I don't want to have to re-open 300 tabs to save
battery. Instead, I use [Lock watch](https://git.mountainofcode.co.uk/lock-watch) to automatically suspend the sink when
I lock my machine, aka I'm not there, and unsuspend it when I return.


On lock:

```bash
#!/bin/bash

SINK_ID=$(pactl list sinks short | grep "Sennheiser_GSP_370" | awk '{print $1}')

if [ -z "$SINK_ID" ]; then
    echo "Error: Sennheiser_GSP_370 sink not found"
    exit 1
fi

pactl suspend-sink "$SINK_ID" 1
```

On unlock:

```bash
#!/bin/bash

SINK_ID=$(pactl list sinks short | grep "Sennheiser_GSP_370" | awk '{print $1}')

if [ -z "$SINK_ID" ]; then
    echo "Error: Sennheiser_GSP_370 sink not found"
    exit 1
fi

pactl suspend-sink "$SINK_ID" 0
```


## Credits

Thanks to <https://h.43z.one/blog/2025-02-12/> for providing the final piece of the puzzle to figure out what was going
on.
