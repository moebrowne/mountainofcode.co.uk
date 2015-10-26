title: "Raspberry Pi Image Manager"
tags:
- Raspberry Pi
- dd
- shell
- automation
- bash
author: Oliver
photos:
- http://mountainofcode.co.uk/images/RPi-Image-Manager.jpg
---

Every time I need to write a new image to my Pi, usually because i've broken it, I have to look up how to write the image, check mounts and find and download the latest version of the image I want.
Even then I have no idea if `dd` is actually progressing or how long i'm going to have to wait... 

There wasn't really anything out there that can take an image name and a location and do the rest for me, now there is!

<!-- more -->

Yeah I know NOOBS solves alot of those problems but I wanted something light weight and with a greater degree of expandabillity...

## Problem 1: Progress Bars!

There is nothing that can't be improved by a progress bar.

After a little research I found some ways of getting a progress report out of `dd` by calling `kill -USR1 {PID}` but it's output isn't ideal and doesn't tell me anything about how long is left just how much data we've moved and you have to call it poll it...

Enter `pv` aka Pipe Viewer. `pv` is awesome, just awesome. It monitors data as it is piped and outputs a nice progress bar, an ETA, average speed, total data etc. It was used heavly in my [Device Benchmarker Project](https://github.com/moebrowne/device-benchmarker). Great, so I can pipe the raw image through `pv` and then into `dd` and i've got a progress bar.

## Problem 2: Image Locations/Downloads

There are images for the Pi all over the internet, the [Raspberry Pi website](https://www.raspberrypi.org/downloads/) does a good of collating a number of them in one place and until recently hosted them. If you go looking in thier [downloads directory](http://downloads.raspberrypi.org/) they still do! Though I suspect they are no longer maintained so wouldn't recommend using them...

A comprehensive up to date list is required...

## [RPi Image Manager](https://github.com/moebrowne/RPi-Image-Manager) Is Born

I built a little tool called [RPi Image Manger](https://github.com/moebrowne/RPi-Image-Manager) that takes an image name and a device to write it to. That's all that's needed. It will then handle the rest for you.

Behind the scenes it downloads the latest version of the image, caches it, extracts it and finally writes the image to your device all the while letting you how long it's going to take or if there is a problem.

```bash
# Clone the repo
git clone https://github.com/moebrowne/RPi-Image-Manager

# Change directory
cd RPi-Image-Manager

# Set the image manager as executable
chmod u+x manager.sh

# Execute!
./manager.sh {IMAGE_NAME} {DEVICE_PATH}
```
