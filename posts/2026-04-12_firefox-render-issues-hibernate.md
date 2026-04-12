# 🦊 Firefox Rendering Issues After Hibernation

#Firefox


I use hibernation a lot. One annoying thing is that after resuming from hibernation Firefox no longer correctly renders
`{html}<canvas>` elements. This was particularly noticeable on Grafana dashboards and the background of this site.

The solution is to restart the Firefox GPU renderer. By default, this means restarting Firefox, which is a PITA, because
the renderer process is embedded in the firefox process. Luckily there is config to move the rendering to a separate
process: set both `layers.gpu-process.enabled` and `layers.gpu-process.force-enabled` to true in about:config.

This new GPU process can then be killed, and Firefox will automatically restart it, which solves all the rendering
issues. This can be automated using a systemctl unit:

`/etc/systemd/system/firefox-gpu-restart-on-resume.service`

```
[Unit]
Description=Kill Firefox GPU process after hibernate/suspend resume
After=hibernate.target suspend.target hybrid-sleep.target

[Service]
Type=oneshot
User=1000
ExecStart=/bin/bash -c 'kill $(ps -ef | awk "/\/opt\/firefox\/firefox-bin/ && / gpu$/ {print \$2}") || true'

[Install]
WantedBy=hibernate.target suspend.target hybrid-sleep.target
```


