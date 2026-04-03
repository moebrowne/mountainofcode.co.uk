# DIY Thermocam v1 Firmware Upgrade 📷

#thermocam
#upgrade


I bought a [DIY Thermocam](https://www.diy-thermocam.net/) kit around 10 years ago, it's great have used it on and off
since. What isn't great is that I never got the USB-mass storage feature working. I thought that old firmware was to
blame, I'd never upgraded it. It was currently running v1.05, the latest is v1.25. Should be simple to upgrade right...
Right?


## The Hunt For Ancient Firmware

The first issue was finding copies of the v1 firmware. I checked the [official site](https://www.diy-thermocam.net/docs/firmware/),
this pointed to the [GitHub repo](https://github.com/maxritter/DIY-Thermocam) which only had copies of v2 and v3. Next I
thought Internet Archive might have an old copy of the site, [it did](https://web.archive.org/web/20160913113851/http://www.diy-thermocam.net/downloads)
but the download links were broken.

Next I turned to scouring the general internet and found not [one](http://diy-thermocam.bplaced.net/Firmware/) but [two](https://github.com/maxritter/DIY-Thermocamdownloads/)
file listings all had dead links. Again to the [Internet Archive](https://web.archive.org/web/20190429104443/http://diy-thermocam.bplaced.net:80/Firmware/),
it had a copy of a [directory listing](/directory-listings-are-cool) of all the firmware versions, except once again the
download links were broken 😠.

The only downloadable copy I could find was on [Softpedia](https://drivers.softpedia.com/get/SCANNER-Digital-CAMERA-WEBCAM/DIY-Thermocam/DIY-Thermocam-Firmware-123.shtml).
I didn't really trust it. The dates and sizes were about right, but I didn't want to risk bricking my camera with
corrupt firmware. Also, they didn't have the latest version: v1.25.

I contacted the author, [Max Ritter](https://www.maxritter.net/imprint), asking if he had copies of the latest firmware.
Literally 5 minutes later, I got a response with several versions of the firmware attached!

For what it's worth, version 1.23 on Softpedia matched the official version (sha256:33e30279a6f94d4892a85e86fc40326a37f7c0753b929f8af1ea717603c61144).



## Now To Actually Write The Firmware

Originally it seems there was a dedicated `Update.exe`, but I think that was probably only required for Windows systems.
I selected 'Firmware upgrade' mode in the camera UI and connected it to my PC. `lsusb` showed a new device:

```
Bus 005 Device 014: ID 16c0:0478 Van Ooijen Technische Informatica Teensy Halfkay Bootloader
```

The brains of the Thermocam is a Teensy 3.2 so flashing was as simple as `teensy_loader_cli --mcu=mk20dx256 -v Firmware.hex`: 

```
Teensy Loader, Command Line, Version 2.2
Read "Firmware.hex": 211920 bytes, 80.8% usage
Found HalfKay Bootloader
Programming................................................................................................................................................................................................................
Booting
```

It worked perfectly. The new firmware had a bunch of new features, and it all seemed a bit faster. USB mass storage mode
still doesn't work though...
