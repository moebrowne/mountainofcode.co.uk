# Gigabyte H110 Not Identifying RAM

#BIOS
#firmware
#PC

I recently got fed up with running out of RAM on my dev machine, it had 16GB, but that doesn't seem to go very far any
more. I purchased and installed the maximum 32GB my Gigabyte H110 motherboard supports.

Annoyingly, it didn't show up, well, not all of it. Only 16GB was detected. This was odd because the [official docs](https://www.gigabyte.com/Motherboard/GA-H110N-rev-10/sp)
explicitly states that it supports up to 32GB. I checked each stick was working by installing them individually; they
were both fine.

Next, I checked the list of [BIOS releases](https://www.gigabyte.com/Motherboard/GA-H110N-rev-10/support#dl), I was
hoping to see a release which mentioned RAM size support, there was nothing. The version I had, F24 (Apr 23, 2018),
this was not the latest. I decided to upgrade. I mostly hoped it would just <magic-sparkle>magically</magic-sparkle>
solve the issue, but it also included a fix for the [PKfail Vulnerability](https://www.binarly.io/pkfail), which was
nice.

Flashing was surprisingly easy using the built-in [Q-Flash](https://www.gigabyte.com/FileUpload/Global/MicroSite/121/flashbios_qflash.pdf)
utility.

## Before

![Before](/images/h110-flash-before.jpg)

## After

![After](/images/h110-flash-after.jpg)
