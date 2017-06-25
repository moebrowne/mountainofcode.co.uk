
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
