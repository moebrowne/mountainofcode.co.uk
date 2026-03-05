# GNOME Disks Benchmark Tapers Off

#performance
#hdd
#benchmark


I've recently been using [GNOME Disks](https://gitlab.gnome.org/GNOME/gnome-disk-utility) to benchmark some hard disks
connected via a USB-SATA adaptor. It was getting results like this:

![](/images/gnome-hdd-benchmark.png)

It would start off about where I expected and then slowly taper off to about half. Messing with the benchmark settings
made no difference, the graph always had the same shape.

Switching to test via `dd` showed a steady 150MB/s:

```
dd if=/dev/sdf of=/dev/null bs=1M count=1024 status=progress
1073741824 bytes (1.1 GB, 1.0 GiB) copied, 7.26574 s, 148 MB/s

dd if=/dev/sdf of=/dev/null bs=1M count=10024 status=progress
2700083200 bytes (2.7 GB, 2.5 GiB) copied, 18.1619 s, 149 MB/s

dd if=/dev/sdf of=/dev/null bs=10M count=1024 status=progress
10737418240 bytes (11 GB, 10 GiB) copied, 71.5707 s, 150 MB/s
```

So what gives? Well, I'd assumed the x-axis was progress, it's not. It's the position on the disk which is being
written/read. I did think it was odd how the latency plot would skip wildly back and forth rather than neatly from
left-to-right.

Another thing that confirms this is that I noticed I could hear the disk physically seeking more often as it approached
the end of the test. [Seems I'm not the only one confused by this](https://www.reddit.com/r/gnome/comments/hr8bkn/how_i_am_supposed_to_read_the_green_scattered/)



## Ok, But Why Is The End Of The Disk Slower?

It's caused by a combination of two things.

Firstly. A hard disk is split up into tracks, each track is a ring of sectors. The tracks on the outside of the disk are 
physically larger and have more sectors per track than the inside. This is called [Zone Bit Recording](https://en.wikipedia.org/wiki/Zone_bit_recording).

Secondly. All tracks complete one revolution in the same amount of time, but the outer tracks have a larger
circumference and so the head passes over them much faster.

These two things together mean that reading/writing to the start of a disk is much faster than the end. If I'd run the
`dd` test for long enough I would've seen the same effect.
