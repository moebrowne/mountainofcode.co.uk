# Debian Power Consumption

#Debian


I recently jumped ship from Ubuntu to Debian, why? The short version is I'd had enough of snaps and apt repo downtime...
It was an easy migration to make and caused very few headaches, but that's not what why I'm writing this post. What was
unexpected was that my computer seemed to be consuming half as much power:

![](/images/debian-power-before.png)

![](/images/debian-power-after.png)

I think the difference is to do with my GPU, an ancient GeForce 560 Ti.

I'd been running Ubuntu 20.04 which supported the official proprietary driver. Debian 13 does not, I don't blame them
it's
```php
//[eval]
echo date('Y') - 2011;
```
years old, it falls back to using the nouveau driver.

To be clear, I don't think that the nouveau driver is 2x as efficient or anything like that. I think the problem was
probably that the official driver had some kind of bug which prevented the card from using the low-power modes.

