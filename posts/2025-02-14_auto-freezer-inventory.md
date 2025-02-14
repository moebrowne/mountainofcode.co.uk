# 🥶 Auto Freezer Inventory

#Automation
#NFC
#First World Problems

After a recent over-ordering of fish I started wondering if there was a technical solution to keeping an inventory
of what's currently in the freezer.

I wanted to be able to get an upto date list on my phone of all the current stocked items. It's obviously too much of a
hassle to go out into the garage to find out what's already in there :P

## Do we have the technology?

![NFC Logo](/images/nfc.png)

My first thought was sticking NFC labels on every box and bag in there, unfortunately most commercially available NFC
tags seem to have a range which is <2cm. The labels would probably be single use too, which kinda sucked. What else is there?

There are Bluetooth LE beacons, these would probably work but the downsides are they are expensive, relatively large and 
have batteries. The size might not be a problem, I bet most of them are tiny devices in a large plastic case which could
be removed but the cost and batteries make this a no-go. At -18C a coin cell is not going to provide much, if any, power.

The next option I came across was [ISO 15693](https://en.wikipedia.org/wiki/ISO/IEC_15693). This is a standard for 
'vicinity cards' which can be read from much further away, more like 1m. The tags seemed to be cheaply available and readers
were available, albeit fairly pricey. The HID Omnikey 5427 was the only reader I could find which had a 'long' range, its
[specs](https://www.smartcardfocus.com/files/CM5427CKGEN2/Datasheet_eat-omnikey-5427-ck-reader-ds-en_3_Jan2024.pdf)
said "Directional antenna enabling long range reading distances up to 2m" but other sources suggest that the range is more
likely 7cm :(

![Barcode](/images/barcode.png)

After some more thought it occurred to me that almost everything in the freezer already had a machine-readable code on it,
a barcode. The main problem being there is no way to remotely read them, they would have to be scanned in and out...
The more I thought about it the more it started to sound like a self-checkout...

## Solving a problem or swapping one?

I couldn't shake the feeling that  attaching, scanning and programming tags or barcodes is simply too much additional
effort to make it worthwhile, I was just swapping one (non)problem for another different problem, not to mention
the hidden costs of bugfixes and upgrades when it inevitably stops working.
