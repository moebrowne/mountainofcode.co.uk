# ForRunners ⭐ Calculation

#running
#FOSS

I recently started running again, always loved it but never really found the time to until recently. I also love data
and record each run I go on. I did a bunch of research to see if I could find a good
<abbr title="Free Open Source Software">FOSS</abbr> app for Android, I wanted something really simple

- No social integrations 
- No cloud tie-in or syncing
- Simple stats, distance, speed, time, etc
- A map of the route
- GPX data export

There are amazingly few apps that meet these criteria. The one I settled on is called ForRunners, it did everything I
needed and had a couple of nice-to-have extras. One feature it had was each run was given a star rating, I didn't really
understand what made the rating go up or down. I was especially confused when one of the runs was given a rating of
-0.1⭐???



## View The Source Luke

After trying to figure out how it was being calculated, it occurred to me that the app is opensource, I can go and see
exactly how it is calculated. Easy. One search for "GitHub ForRunners" later turned up the [GitHub repo](https://github.com/brvier/ForRunners),
which had subsequently been moved to [GitLab](https://gitlab.com/brvier/ForRunners).

As I started browsing the repo I couldn't believe that it was built using web tech, [I know this](https://www.youtube.com/watch?v=dFUlAQZB9Ng).
I started by locating the views where the star rating was displayed and work backwards from there to find the algorithm.

The view I was interested in was [sessions.html](https://gitlab.com/brvier/ForRunners/-/blob/master/www/templates/sessions.html),
specifically these lines:

```html
<span class="badge badge-lower" style="display: block; vertical-align: middle; text-align:center; font-size:11px; width: 30px; margin-left:5px;">
    {{ session.overnote }} <i class="icon ion-star"></i>
</span>
```

Bingo! `overnote` is the variable I was looking for. Tracing this back, it seems the view is rendered by
[`controller.js`](https://gitlab.com/brvier/ForRunners/-/blob/master/www/js/controllers.js), which gave me what I was
after:

```js
session.overnote = (
    parseInt(gpxspeedwithoutpause) *
    1000 *
    (miliseconds / 1000 / 60) *
    0.000006 +
    (Math.round(eleUp) - Math.round(eleDown)) * 0.04
).toFixed(1);
```

It references a bunch of variables defined further up, but it boils down to:

```
SpeedInKmPerHour × DurationInMinutes × 0.006 + TotalElevationChange × 0.04
```

Taking data from one of my actual runs and plugging it in matched what I saw in the app:

```
11.26Km/h × 21mins × 0.006 + (73m - 80m) × 0.04 = 1.1⭐
```

This calculation broadly makes sense, go faster or run for longer you get a higher rating. At first, I was surprised
that there were no points for distance, but it dawned on me that Speed x Time is distance that made me wonder... If we
convert the time into hours we get `SpeedInKmPerHour × DurationInHours × 0.36`, which can then be simplified to
`DistanceInKm × 0.36` because distance = speed × time.

I assume they chose to calculate it the way they did because they wanted to use the speed ignoring any stops.



## So Why The Negative Rating?

As to why I got a negative rating, I think this is caused by GPS error. If `eleDown` is ever greater than `eleUp` then 
there will be a negative term, this is possible if you end the run downhill of where you started. All my runs have been
circular, so the total up and total down should be equal, but the GPS error, especially at the start, will be relatively
high and give the wrong elevation.
