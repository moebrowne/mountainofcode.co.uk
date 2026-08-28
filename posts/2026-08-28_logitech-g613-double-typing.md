# Logitech G613 Double Tyyping

#keyboard

Recently my keyboard started double typing letters, it would only happen about 10% of the time and only on certain
letters. It was very much a case of a slowly boiled frog. At first it was infrequent enough to write it off as something
I was doing.

Without realising why, I noticed that I was finding typing a bit frustrated, I was also typing a lot slower. I realise
now that I had started expecting mistakes and was focusing on looking for them, so I could correct them.

The keyboard in question is a Logitech G613, before now it'd been a very reliable keyboard. I wanted to fix it, and
started keeping note of the words which contained mistakes.

```
rrespected
waant
sett
iit
forr
iit
Debiian
II’ve
tto
Inteernal
inteerested
wwheen
stattic
gitt
iis
therre
aree
managerr
iif
IInstall
createed
starteed
Createe
importantt
customisatiions
timee
veersion
dockerr
Prress
foreveerr
foreverr
triixe
forrever
baash
passworrd
aare
togetheer
locaal
tthe
serveer
correect
tiimes
norrmal
```

The issue was primarily with: e, r, i and t. At first this pointed at the top row being faulty, but, O and P were always
correct.

I searched for the issue, and plenty of people had similar issues and offered solutions. I tried all sorts, from simply
changing the batteries, cleaning the battery contacts, using a different USB 2/3 port and cleaning the keys. One
suggestion which gave me hope was to re-flow all the 0-ohm jumpers on the back of the board. It didn't work, nothing
made a difference.

I was sure the problem was with the hardware because the problem persisted across two different PCs with different
operating systems.

I was left to conclude that the switches had simply worn out. This made sense when I realised that it was nearly 10
years old and was used pretty much daily for work and pleasure. I wondered if I could estimate the number of keystrokes
I'd made in that time.



## Guestimation Time!

I bought it in Nov 2017, say 100 months or 3000 days ago, this is all going to be very approximate... Let's say between
working from home and my free time I was at my computer 7 hours a day. Let's say I spent 5 hours of that actually typing,
and say I averaged 40wpm, and that a word is 8 characters. That would be 3000 &times; 5 &times; 60 &times; 40 &times; 8
= ~300 million.

Obivously, not all keys used equally. E is the most common letter with a frequency of ~12% or for me 35 million presses.
This is a little short of the 70 million keystrokes which the Romer-G switches are advertised at handling, but the error
bars on my estimates are pretty large.



## Monitoring

I thought it would be interesting and fun to track the number of keypresses on my new keyboard. I set up a service which
writes metrics to a text file which is intended to be picked up by [node exporter](https://github.com/prometheus/node_exporter).
It uses `libinput`s debug mode to get a stream of key presses (`apt install libinput-tools`).

I also wanted to track my typing speed over time because I'd recently started using the excellent <https://www.keybr.com/>
to try and learn to type properly and quicker.

It also tracks mouse events, because why not 😛

```bash
#!/usr/bin/env php
<?php
declare(strict_types=1);

$promFile = '/var/lib/prometheus/node-exporter/input_events.prom';
$promTmp  = $promFile . '.tmp';

@mkdir(dirname($promFile), 0755, true);

function countKey(string $device, string $control): string {
    return $device . "\x00" . $control;
}

$counts = [];

// Load existing metrics, if present.
if (is_readable($promFile)) {
    $fh = fopen($promFile, 'r');
    while (($line = fgets($fh)) !== false) {
        if (preg_match('/^input_events_total{device="([^"]+)",button="([^"]+)"} (\d+)$/', rtrim($line, "\n"), $m)) {
            $counts[countKey($m[1], $m[2])] = (int) $m[3];
        }
    }
    fclose($fh);
}

function writeMetrics(array &$counts, string $tmp, string $out): void {
    $lines = [];
    $lines[] = '# HELP input_events_total Total input events (keys, clicks) recorded system-wide since service install.';
    $lines[] = '# TYPE input_events_total counter';
    foreach ($counts as $ck => $n) {
        [$device, $control] = explode("\x00", $ck, 2);
        $lines[] = sprintf('input_events_total{device="%s",button="%s"} %d', $device, $control, $n);
    }
    file_put_contents($tmp, implode("\n", $lines) . "\n");
    rename($tmp, $out);
}

$devname = []; // node => device name

$cmd = 'stdbuf -oL libinput debug-events --show-keycodes';
$proc = popen($cmd, 'r');
if ($proc === false) {
    fwrite(STDERR, "Failed to start libinput\n");
    exit(1);
}

while (($line = fgets($proc)) !== false) {
    $line = rtrim($line, "\n");
    $fields = preg_split('/\s+/', trim($line));

    if (count($fields) < 2) {
        continue;
    }

    $node = ltrim($fields[0], '-');
    $eventType = $fields[1];

    if ($eventType === 'DEVICE_ADDED') {
        $name = [];
        for ($i = 2; $i < count($fields) && $fields[$i] !== 'seat0'; $i++) {
            $name[] = $fields[$i];
        }
        $devname[$node] = implode(' ', $name);
        continue;
    }

    if ($eventType === 'KEYBOARD_KEY' || $eventType === 'POINTER_BUTTON') {
        $code   = $fields[3] ?? '';
        $action = rtrim($fields[5] ?? '', ',');
        if ($action !== 'pressed') {
            continue;
        }

        $device  = $devname[$node] ?? 'unknown';
        $control = preg_replace('/^(KEY_|BTN_)/', '', $code);

        $ck = countKey($device, $control);
        $counts[$ck] = ($counts[$ck] ?? 0) + 1;
        writeMetrics($counts, $promTmp, $promFile);
    }

    if ($eventType === 'POINTER_SCROLL_WHEEL') {
        $device  = $devname[$node] ?? 'unknown';

        $ck = countKey($device, 'scroll');
        $counts[$ck] = ($counts[$ck] ?? 0) + 1;
        writeMetrics($counts, $promTmp, $promFile);
    }
}

pclose($proc);
```

This creates metrics which look like this:

```prom
# HELP input_events_total Total input events (keys, clicks) recorded system-wide since service install.
# TYPE input_events_total counter
input_events_total{device="Logitech USB Receiver Keyboard",button="B"} 149
input_events_total{device="Logitech USB Receiver Keyboard",button="C"} 378
input_events_total{device="Logitech USB Receiver Keyboard",button="D"} 261
input_events_total{device="Logitech USB Receiver Keyboard",button="E"} 756
input_events_total{device="Logitech USB Receiver Keyboard",button="RIGHTBRACE"} 7
input_events_total{device="Logitech USB Receiver Keyboard",button="F"} 144
input_events_total{device="Logitech USB Receiver Keyboard",button="LEFT"} 461
```

and graphs like this:

![](/images/keyboard-inputs-graph.png)
