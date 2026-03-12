# 🌡 Reading System Temperature Sensors

#monitoring


I always thought that to read temperature sensors on a Linux machine, you had to reach for a package like `sensors`. You
don't.

Like lots of things in Linux, the standard temperature sensor values are exposed as virtual files:

`cat /sys/class/thermal/thermal_zone*/temp`

```
27800
20000
88000
88000
```

That will give you the raw sensor value in milli&deg;C, so divide by 1,000 to get &deg;C. You can get the name of each
sensor too.

`cat /sys/class/thermal/thermal_zone*/type`

```
acpitz
INT3400 Thermal
TCPU
x86_pkg_temp
```


