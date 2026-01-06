# USB RAID Speeds On The Raspberry Pi

#benchmark
#mdadm
#nas
#raid
#raspberry pi
#usb

![](/images/raspberry-pi-raid.png)

Using a Raspberry Pi seems to make a lot of sense if you're in the market for a small NAS server, it's low cost, 
low profile, low energy etc...

## Is It Up To It?

The only possible problem is the storage the only way to attach any real storage to a Pi is via USB.
USB 2.0 has a maximum theoretical throughput of 480Mbits/s with but I don't think you'll ever get anywhere near that speed.

We only really need to achieve around 10MB/s as then the ethernet connection becomes the bottleneck

## Benchmarking

I wanted to see what kind of throughput I could actually get with USB devices connected to a Pi so I wrote a [device benchmarker](https://github.com/moebrowne/device-benchmarker) 
tool that is able to measure both the read and write speeds of one or more block devices.

### The Setup

I setup my Raspberry Pi B in the following  way:

- Clean install of Raspbian 2015-02-16
- Ran apt-get upgrade on 00:00 10/04/15
- 2x Verbatim 4GB memory sticks `ID 18a5:0302 Verbatim, Ltd Flash Drive`

### The HDDs

Ok memory sticks aren't HDDs but they are what I had to hand at the time.

I formatted both the memory sticks to ext4 using gparted and ran my [device benchmarker](https://github.com/moebrowne/device-benchmarker) on each of them 
individually then both in parallel.

### The Results

I ran both dd-read and dd-write tests 10 times and then took an average:

| Device    | Reading (MB/s) | Writing (MB/s) |
|-----------|----------------|----------------|
| /dev/sda1 | 14.98          | 5.23           |
| /dev/sdb1 | 14.04          | 5.25           |
| /dev/sd*  | 12.86          | 5.05           |

The results for the combined `/dev/sd*` are the speeds of the individual devices when tested at the same time.

The write speeds seemed a little slow to me, I knew they were going to be slower than the read speed but 3x slower? After
a quick Google I stumbled upon [http://usbspeed.nirsoft.net](http://usbspeed.nirsoft.net), it's basically a crowd-sourced
database of USB memory stick read write speeds. I looked up the entries they had for my USB drive and took an average,
they pretty much matched up so it seems that's what they capable of.

### What Next?
Interestingly when both devices were written to/read from in parallel the speed seemed to only drop a little meaning the
USB bus wasn't being saturated, the drives are the bottleneck. Could we boost the read/write speeds if we were able to 
treat both devices as a single device, in RAID0 for example?

I realise going after a higher read speed is a little pointless as at 14MB/s we would be saturating the ethernet
connection which is only capable of an absolute maximum of 10MB/s but there is room for a 2x increase in the write
speeds.

## RAID

I'm going to use MDADM to setup a RAID0 array and then run the benchmarks again to see if we can achieve a write speed
of >10MB/s

### MDADM Array Setup

Installing MDADM was as simple and painless as:

```
sudo apt-get install mdadm
```

Once MDADM was installed I assembled a new RAID0 array from both my drives and formatted the array to `ext4`, the same 
filesystem as before:

```
# Create the array
sudo mdadm --create --verbose /dev/md0 --level=stripe --raid-devices=2 /dev/sda1 /dev/sdb1

# Format the new array as ext4
sudo mkfs -t ext4 /dev/md0
```

### Benchmarking

I re-ran exactly the same benchmarks as before and got the following results:

| Device    | Reading (MB/s) | Writing (MB/s) |
|-----------|----------------|----------------|
| /dev/md0  | 27.92          | 8.92           |



That's ~192% the read speed and ~170% the read speed.
More importantly the write speed is nearing the 10MB/s maximum the Pis ethernet controller can handle!

## MOAR SPEED!!

The memory sticks I was using definitely aren't the fastest out there and given the realistic through put of USB 2.0, 
which according to [wiki](http://en.wikipedia.org/wiki/USB#USB_2.0) is around 35MB/s, there's definitely still a lot of room for improvement.

But what about that 10MB/s limit of ethernet? Gigabit of course! I'm not the first to try this, see [this](http://www.midwesternmac.com/blogs/jeff-geerling/getting-gigabit-networking) 
article for example, I have a Raspberry Pi 2 so maybe I'll try hooking to a USB Gigabit adapter and add 2 or 3 faster 
USB drives and see if I can max out the USB bus.