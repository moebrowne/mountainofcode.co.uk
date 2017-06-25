
I am very much an advocate for 2 factor authentication tokens or keys such as a [Yubikey](https://www.yubico.com/),
 these devices can interface with web browsers through a Javascript API that web browsers expose or in
 the case of Firefox not expose.

There is a community made extension that fills this gap until the Firefox devs get the U2F JS API implemented in
 [version 57 or 58](https://wiki.mozilla.org/Security/CryptoEngineering#Web_Authentication) however I
 was never able to get it to work, no matter what I tried and no matter how many times I ran the [test](https://u2f.bin.coffee/)
 it just kept popping up with a message saying "Please plug in your U2F device".

Then I found the source code on GitHub and the last line in the README contained the key...

<!-- more -->

## "Permissions tweaks for Linux"

The very last section of the README in the [U2F Support Extensions repo](https://github.com/prefiks/u2f4moz)
 is entitled "Permissions tweaks for Linux" and contains a link to a list of uDev rules supplied by 
 Yubico that need to be added for Firefox to be allowed to access Yubikeys on unix systems.

## The Where & How

The udev rules required can be copied from the [Yubico/libu2f-host](https://github.com/Yubico/libu2f-host/blob/master/70-u2f.rules)
 repo into `/lib/udev/rules.d/70-u2f.rules` on your local system. Once copied there the new rules can be
 reloaded using the following command, which needs to be run as root:

```
udevadm control --reload-rules && udevadm trigger
```

Now once Firefox is restarted it should be able to talk to a Yubikey and pass the [U2F support checker](https://u2f.bin.coffee/)?
