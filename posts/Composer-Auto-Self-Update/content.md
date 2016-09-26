I've always thought the Linux way of everything auto-updating for you, albeit after asking you first, was the best way forward so it always felt kind of wrong having to run `composer self-update` manually.

What better tool is there to periodically run a script than cron. It's as simple as adding the following to a file located, at least on Ubuntu, here: `/etc/cron.daily/composer`

```bash
#!/bin/sh
    
# Update Composer to the lastest version
composer self-update
```

It's probably a good idea to redirect the output to a log file with a timestamp but i'm not that worried. Composer complains at you if it's older than 30 days anyway:

    Warning: This development build of composer is over 30 days old.
    It is recommended to update it by running "composer self-update" to get the latest version.