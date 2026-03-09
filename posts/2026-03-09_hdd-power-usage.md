# Hard Disks Use A Surprising Amount Of Power

#HDD
#power

It's been possible to spin down HDDs to save power forever, but does it actually make a meaningful difference?

The drives in question are a pair of Western Digital 1TB drives. They're used for long-term storage and get very few
reads/writes so could easily be spun down for 99% of the time without me noticing.

![](/images/hdd-power-use.png)

From 64 W down to 58 W, about a 10% decrease. 3 W per drive, not loads but more than I expected.

Another, unexpected, benefit was noise. The disks aren't loud by any measure, but they did make an audible noise which
is now gone.

For anyone interested I used `smartctl -s standby,now /dev/sdx` to put the drives to sleep.



## Hard To Measure

Taking these measurements was much harder than I thought it would be. I had to set the drives to sleep after a delay
(`sleep 10m && smartctl -s standby,now /dev/sdx`) and then left the machine to idle. This was necessary because any
CPU/GPU time would cause large spikes and obscure any changes.

Even then the graph above has a small amount of averaging.

With hindsight, I think it would've been easier, and probably more accurate, to put a current clamp on the power lines
to the HDDs.
