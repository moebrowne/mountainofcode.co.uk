# Folding@Home Thermostat 

#Folding@Home
#enviroment
#Prometheus

I have run a Folding@Home client for over 15 years. I love the idea of being able to contribute to medical science at the
cost of a few KWh, It feels much more tangible that donating the equivalent in £. It has also had the benefit of keeping
me warm at home.

When I lived in a single bed flat I was able to keep the place warm using just the folding PC, at least most of the time,
there were times in the summer where it got too hot and had to be paused. While electric heating isn't the most
cost-effective in this case it serves two purposes and in my opinion that gives it more than double the value.

The setup I run now is a bit different, the thing I want to talk about is how it's now thermostatically controlled.

![Enviro Indoor Module](/images/enviro.jpg)

I have a number of [Enviro modules](https://shop.pimoroni.com/products/enviro-indoor) around the house and outside, these
modules are awesome, they aren't exactly cheap but they are small, filled with sensors, open-source, configurable and
only need their batteries changing once a month. I love them. They are also expandable, the one I have in my office has
a [NDIR CO<sub>2</sub> sensor](https://shop.pimoroni.com/products/adafruit-scd-30-ndir-co2-temperature-and-humidity-sensor-stemma-qt-qwiic).

The modules feed their data into a centralised Prometheus instance and while it's fun to plot the data and see the trends
the data can be used by other systems to make decisions. Like deciding if it's too warm to run F@H at the moment.


## The Code

The whole setup is less than 50 lines of BASH which is called by cron.

```bash
#!/bin/bash

set -e

TARGET_TEMP="20" #°C
HISTO="1" #°C
MAX_TEMP="26" #°C

CURRENT_TEMP=$(curl --request POST \
  http://192.168.1.200:8086/api/v2/query?orgID={REDACTED}  \
  --silent \
  --header 'Authorization: Token {REDACTED}' \
  --header 'Accept: application/json' \
  --header 'Content-type: application/vnd.flux' \
  --data 'from(bucket: "{REDACTED}")
  |> range(start: -3h)
  |> filter(fn: (r) =>
    r._measurement == "temperature"
  )
  |> aggregateWindow(every: 3h, fn: mean)' \
 | head -n 2 \
 | tail -n 1 \
 | awk -F "\"*,\"*" '{print $7}' \
 | grep -oE '^[0-9]+')


# Way too hot, stop folding
if [[ $CURRENT_TEMP -ge $MAX_TEMP ]]; then
    /usr/bin/fahctl pause
    exit
fi

# Too hot, finish the current task
if [[ $CURRENT_TEMP -ge $(( $TARGET_TEMP+$HISTO )) ]]; then
    /usr/bin/fahctl finish
    exit
fi

# Too cold, start Folding
if [[ $CURRENT_TEMP -lt $(( $TARGET_TEMP-$HISTO )) ]]; then
    /usr/bin/fahctl fold
    exit
fi
```

This tries to maintain a temperature of `{bash}$TARGET_TEMP` and applies a hysteresis of `{bash}$HISTO`

The algorithm goes something like this:

1. Get the average temperature over the last 3 hours.
2. If it's too cold start folding
3. If it's too hot finish the current task and then pause
4. If it's waay too hot then stop folding
5. goto 1.

I use cron to call this script every 15 minutes.

![Graph of temperature](/images/enviro-temp.png)

It does a fairly good job, but I think the averaging period might need to
be reduced because it has a tendency to under/over shoot the target temp. Perhaps relying on just the hysteresis would be
enough?
