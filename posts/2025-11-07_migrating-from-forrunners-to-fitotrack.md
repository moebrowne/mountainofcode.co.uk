# Migrating From ForRunners to FitoTrack

#data
#excersise

I've been using [ForRunners](https://gitlab.com/brvier/ForRunners) to track my runs for a little while, it did
everything I needed and was beautifully simple. The only complaint I really had was that it took an annoyingly long time
to get a GPS fix, sometimes it didn't seem to even be trying to use the GPS permission.

I persevered for a while, it was only a minor annoyance, but after I came across [FitoTrack](https://codeberg.org/jannis/FitoTrack)
which satisfied the same core requirements of simplicity, opensource and GPX export, I decided to switch.

The migration was supposed to be simple, export GPX files from ForRunner and import into FitoTrack...


## Problem 1. Format Discrepancies

I fed the files into FitoTrack's import UI, and it threw an ugly error:

![Error The data import has failed. Cannot deserialize value of type “int” from String "x": not a valid "int" value at \[Source: \(android.os.ParcelFileDescriptor$ AutoCloselnputStream\); line: 14, column: 53\] \(through reference chain: de.tadris.fitness.util.gp x.Gpx\["trk"\]->java.util. ArrayList\[0\]->de.tadris.fitne ss.util.gpx.Track\['trkseg'\]->java.util. ArrayList\[0\]->de.tadris fitness.util.gpx. TrackSegment\["trkpt'\]->j ava.util. ArrayList\[0\]->de.tadris fitness.util.gpx.Tra ckPoint\['extensions'\]->de.tadris.fitness.util.gpx.TrackPointExtensions|'TrackPointExtension'\]->de.t adris. fitness. util.gpx.GpxTpxExtension\["hr"\]\) ](/images/FitoTrack-import-error-gpx-extension.png)

This meant very little to me, but I was grateful that they didn't give a generic 'Import failed' error, this gave me
something to go on. Looking closer, there were some clues to where the problem lay: `gpx.GpxTpxExtension["hr"]` seemed
to match with a suspicious value in the GPX file:

```xml
<extensions>
    <gpxtpx:TrackPointExtension>
        <gpxtpx:hr>x</gpxtpx:hr>
        <gpxtpx:accuracy>3</gpxtpx:accuracy>
        <gpxtpx:cad>x</gpxtpx:cad>
        <gpxtpx:power>x</gpxtpx:power>
        <gpxtpx:stryde>x</gpxtpx:stryde>
    </gpxtpx:TrackPointExtension>
</extensions>
```

I assume `hr` stands for heart rate and as I don't have a heart rate monitor, I suspect that `x` was being added as a
placeholder value. I guess the whole tag should've been omitted rather than adding a placeholder. This was easily fixed,
a simple find and replace removed the invalid values. This fixed the error.


## Problem 2. Minimum Distance Between Points

![The data import has failed. NOT NULL constraint failed: workout.avgSpeed (code 1299 SQLITE_CONSTRAINT_NOTNULL)](/images/FitoTrack-import-error-speed.png)

This problem was more challenging to figure out... Not only was the error message not very helpful, `avgSpeed` didn't
appear in the GPX file at all, but it also didn't affect all the GPX files, some imported correctly.

I first created a run in FitoTrack, exported it and checked that imported ok; it did. Now I had a known good, I compared
the known good with one of the files which failed to import. There were a few differences. I started whittling them
down, removing parts until it started working. I removed a lot. It got to the point where I only had 3 trk points, with
lat and lng only, it would still fail to import.

As a guess, I tried messing with the GPS coords and increasing the difference between them finally made the import
succeed! It seemed like there needed to be a minimum distance between points, I reduced the difference between the
points until it started failing to import. It turns out the magic distance is 5m, if any points in the workput are less
than 5m apart, FitoTrack will not import the file.

This was not something which could be fixed with a find and replace, the GPS coordinate pairs needed converting into
absolute distances. I needed a conversion script.

This is what I came up with (it also fixes problem 1):

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use phpGPX\Helpers\GeoHelper;
use phpGPX\Models\GpxFile;
use phpGPX\Models\Metadata;
use phpGPX\Models\Point;
use phpGPX\Models\Segment;
use phpGPX\Models\Track;
use phpGPX\phpGPX;

function convert(string $filePath): string
{
    $gpx = new phpGPX();

    $file = $gpx->load($filePath);

    $fitoTrackGpx = new GpxFile();
    $fitoTrackGpx->creator = 'FitoTrackConverter';
    $fitoTrackGpx->metadata = new Metadata();
    $fitoTrackGpx->metadata->name = $file->metadata->time->format('d M Y H:i');
    $fitoTrackGpx->metadata->time = $file->metadata->time;


    foreach ($file->tracks as $track) {
        $fitoTrackTrack = new Track();
        $fitoTrackTrack->name = $file->metadata->time->format('d M Y H:i:s');
        $fitoTrackTrack->type = 'running';
        $fitoTrackTrack->source = 'ForRunner';

        foreach ($track->segments as $segment) {
            $fitoTrackSegment = new Segment();

            $lastPoint = null;
            foreach ($segment->getPoints() as $forRunnerPoint) {
                if ($lastPoint === null) {
                    $lastPoint = $forRunnerPoint;
                    continue;
                }

                $distance = GeoHelper::getRawDistance($lastPoint, $forRunnerPoint);

                if ($distance >= 5) {
                    $fitoTrackPoint = new Point(Point::TRACKPOINT);
                    $fitoTrackPoint->latitude = $forRunnerPoint->latitude;
                    $fitoTrackPoint->longitude = $forRunnerPoint->longitude;
                    $fitoTrackPoint->elevation = $forRunnerPoint->elevation;
                    $fitoTrackPoint->time = $forRunnerPoint->time;

                    $fitoTrackSegment->points[] = $fitoTrackPoint;

                    $lastPoint = $forRunnerPoint;

                    continue;
                }
            }

            $fitoTrackTrack->segments[] = $fitoTrackSegment;
        }

        $fitoTrackGpx->tracks[] = $fitoTrackTrack;
    }

    $pathInfo = pathinfo($filePath);
    $saveDir = $pathInfo['dirname'] . '/converted';
    $savePath = $saveDir . '/' . $pathInfo['basename'];

    if (is_dir($saveDir) === false) {
        mkdir($saveDir);
    }

    $fitoTrackGpx->save($savePath, \phpGPX\phpGPX::XML_FORMAT);

    return $savePath;
}

foreach (glob('/path/to/exported/files/*.gpx') as $gpxPath) {
    echo $gpxPath . PHP_EOL;
    convert($gpxPath);
}
```

I used the [sibyx/phpgpx](https://github.com/sibyx/phpgpx) library to do the heavy lifting for coord conversion.

After running this, all the files imported successfully.

