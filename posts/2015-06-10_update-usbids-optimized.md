# Caching USB ID Database Updates

#bash
#caching
#usb

In Linux you can run `lsusb` on the command line to list all currently connected USB devices but you may find a number 
of the entries give a pretty generic or blank name for the device.

This is simply because those devices aren't listed in your copy of the USB ID database, this database can be easily 
updated with USBUtils `update-usbids`.

Awesome problem solved... So what's the problem?
You will notice that if you run `update-usbids` and then run it again right away you will download the database twice, 
but I already have an upto date copy of the database...

## The USB ID Database

### Where Is It?

The USB ID database is stored locally, at least on Ubuntu, as a text file located here: `/var/lib/usbutils/usb.ids`.

### How Does It Update?

When you call `update-usbids` literally all that happens is a backup is made of your current database and then the 
latest copy of the database is downloaded from `http://www.linux-usb.org/usb.ids`.

## HTTP Headers

If you inspect the headers returned when you issue a GET request for `http://www.linux-usb.org/usb.ids` you'll see both
`Last-Modified` and `Etag` headers but not only that you'll also see an `Expires` header to!

We have all we need to check if our database is upto date with [linux-usb.org](http://www.linux-usb.org/) rather than just blindly downloading
it every time.

## The New `update-usbids`

I put together a little bash script that can be used in place of `update-usbids` that makes use of the caching headers 
that are exposed to us, you can get a copy from GitHub [here](https://github.com/moebrowne/update-usbids-optimized)

### How it works

- Make a HEAD request for the compressed version of the database `http://www.linux-usb.org/usb.ids.gz`.
- Check if the returned `Etag` header matches the `Etag` we stored from last time we requested an update.
- If it does match great! Nothing more required and it cost us only a couple hundred Bytes!
- If it doesn't match there's something new in the database!
- Create a backup of our current copy of the database just in case.
- Download the new version of the database.
- Keep a note of the `Etag` of this database.

Also it'll draw pretty progress bars for you courtesy of `pv` 

## Conclusion

I'm a little surprised this hasn't already made it into the source of usbutils, seems kind of an obvious optimisation...
It allows you to always have an upto date USB device list, especially if you alias `lsusb` to `update-usbids && lsusb`!

While it's true the whole database once Gzip has finished with it is, at the time of writing, 246kB there's not much 
bandwidth or time to be saved, at least not from the users point of view.

What about from the servers point of view? Then it's a numbers game. I couldn't find a number of Ubuntu users in the 
world but say for the sake of argument it's 20 million. If just 5% of these people ran `update-usbids` once a week 
that's over 234GB of bandwidth required on the server end every week. I have no idea how often the database updates 
but given the `Expires` header is set to 3 days in the future, you could at least half that 234GB number and save time 
and bandwidth for the users to!

This is only going to become more pronounced as the number of people using USBUtils grows and the number of entries in 
the database grows...

### It's Open Source, So Why Not Submit It?

In short I have no idea how... I had a Google and found the source for USBUtils but found no way of contributing my 
code...
