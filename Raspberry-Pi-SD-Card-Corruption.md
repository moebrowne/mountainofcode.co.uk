title: "Raspberry Pi SD Card Corruption"
tags:
- Raspberry Pi
- SD Card
- Raspbian
author: Oliver
photos:
- /images/RPi-corrupt.jpg
---

Just the other day I was helping a friend debug a really weird problem he was having with a his Raspberry Pi (model B),
it turned out to be a really simple easy to fix problem but hard to debug... 

<!-- more -->

## The Hardware

* Raspberry Pi B
* 16GB Kingston SD Card
* 2A 5v USB Power Supply
* Rasbian Jessie (Latest version)

## The Symptoms

The main symptom was during a long multi-package apt install a number of segmentation faults would be thrown and
complaints about strange errors in random perl files, and they were different each time.

Looking at the offending perl files showed lines of code that overlapped and blocks of code that repeated.. strange.
And then on reboot, because when didn't turning a computer off and on again help, the whole system wouldn't boot...

A little digging in the file system after another run of apt showed it wasn't just the `perl` files that were corrupt, 
`fstab` was full of a completely different config file so no wonder it didn't boot.


## Power Supply?

My first thought was that the SD being corrupt due to the Pi/SD Card not getting enough power, and while the
power supply was capable to supplying 2A at 5v but how much of that was being dropped in the lengthy cable?

There are 2 test points on the RPi B, `TP1` and `TP2`, the voltage between these points should be 5v +/- 0.25V,
10 minutes and multimeter later showed the voltage was ok before boot, during boot and then while idle, but I wondered
if there would be voltage drop when the hardware was heavily loaded, but nope, all was fine.


## SD Card

Next my suspicion turned to the SD card.

We had already tried several SD cards in the Pi, but they were all exactly the same. This was easy to test as I had a
known good SD card, I popped it in to the same Pi with the same power supply and the same image.

BINGO! All the segmentation faults went away, the config files weren't getting corrupt everything was working
as expected.

### But Why?

Well turns out this is a known issue with the SD card that had been picked. I found this out from the really useful SD
card compatibility list at [elinux.org](http://elinux.org/RPi_SD_cards). Searching for our SD card ID `SD10G2/16GB`
showed reports of people having problems with the same one.

