My PC is on literaly 24/7 as I run a FAH (Folding At Home) client and Transmission to seed the RPi and Ubuntu image torrents, but when I want to use my machine I have to pause them both as they bog the machine down.

This was a manual task I had to do every time I sat down and I had to remember to set them both going again when I was done, something I didn't always remember to do at 3AM after a session of "I'll just do a little more..."

Out of habit I lock my PC whenever I leave it and I thought that was an ideal trigger to pause/resume the FAH client and Transmission!

<!-- more -->

## Listening For The Lock/Unlock Events

After spending a while trawling through many StackExchange and AskUbuntu questions, all from at least a year ago, for how to listen for the lock/unlock events I couldn't get anything to work. It didn't help that i'm using MATE and as I later found out it's MATE that emits the event...

### Success!

After some guess work I managed to get it working based off [this](http://askubuntu.com/questions/204073/how-to-run-script-after-resume-and-after-unlocking-screen) AskUbuntu question.

The key to it is the following line. Note I changed `org.gnome.ScreenSaver` to `org.mate.ScreenSaver`

```bash
dbus-monitor --session "type='signal',interface='org.mate.ScreenSaver'"
```

### Output

When you run the `dbus-monitor` command above and you then lock then unlock your screen you'll get an output similar to this:

```html
signal sender=org.freedesktop.DBus -> dest=:1.404 serial=2 path=/org/freedesktop/DBus; interface=org.freedesktop.DBus; member=NameAcquired
   string ":1.404"
signal sender=:1.29 -> dest=(null destination) serial=111 path=/org/mate/ScreenSaver; interface=org.mate.ScreenSaver; member=ActiveChanged
   boolean true
signal sender=:1.29 -> dest=(null destination) serial=112 path=/org/mate/ScreenSaver; interface=org.mate.ScreenSaver; member=ActiveChanged
   boolean false
```

The intersting parts are the `boolean true` and `boolean false` these are outputted when the lock state changes and we can use them to fire our scripts.

## The Code

I have written a little script that handles the setting up of the event listener and makes adding scripts to run easy, you can get a copy of it from my [GitHub](https://github.com/moebrowne/lock-watch).

The only setup required is to set this script running as a deamon.

### FAH

The scripts I added for pausing/resuming the FAH client are pretty simple:

```bash
# Pause Folding slot 01
/usr/bin/FAHClient --send-pause 01

# Resume Folding slot 01
/usr/bin/FAHClient --send-unpause 01
```

### Transmission

For pausing/resuming torrents in Transmission I had to do a little more.

I had to install an additional package `transmission-cli` and configure Transmission to allow remote access through Edit > Preferences > Remote > Allow remote access but after that you can then do:

```bash
# Pause all torrents
transmission-remote --torrent all --stop

# Resume all torrents
transmission-remote --torrent all --start
```
