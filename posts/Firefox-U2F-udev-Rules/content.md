
I am very much an advocate for 2 factor authentication tokens or keys such as a [Yubikey](https://www.yubico.com/),
 these devices can interface with web browsers through a Javascript API that web browsers expose or in
 the case of Firefox not expose.

There is a community made extension that fills this gap until the Firefox devs get the U2F JS API implemented in
 [version 57 or 58](https://wiki.mozilla.org/Security/CryptoEngineering#Web_Authentication) however I
 was never able to get it to work, no matter what I tried and no matter how many times I ran the [test](https://u2f.bin.coffee/)
 it just kept popping up with a message saying "Please plug in your U2F device".

Then I found the source code on GitHub and the last line in the README contained the key...

<!-- more -->

- Test site: https://u2f.bin.coffee/
- Plugin
  - Download: https://addons.mozilla.org/en-US/firefox/addon/u2f-support-add-on/
  - Source: https://github.com/prefiks/u2f4moz
- Always just says "Please plug in your U2F device"
- Need to add udev rules from Github (https://github.com/Yubico/libu2f-host/blob/master/70-u2f.rules)
  - `/lib/udev/rules.d/70-u2f.rules`
  - `udevadm control --reload-rules && udevadm trigger`
  - Restart Firefox
  - Copy of official rule list (https://github.com/Yubico/libu2f-host/blob/master/70-u2f.rules)
  - Older rule set for udev older than 188 here: https://github.com/Yubico/libu2f-host/blob/master/70-old-u2f.rules
    - How do you get the version?
