# 100 Million Row Challenge

#PHP
#performance

[Brendt](https://www.youtube.com/@phpannotated) recently [issued a challenge](https://stitcher.io/blog/100-million-row-challenge)
to the PHP community:

> The goal of this challenge is to parse 100 million rows of data with PHP, as efficiently as possible.

The full rules and prizes can be found [here](https://github.com/tempestphp/100-million-row-challenge).

This was somewhat fortunate timing for me as just the day before I had read [this excellent article](https://dev.to/realflowcontrol/processing-one-billion-rows-in-php-3eg0)
about approaching a similar problem.

I didn't expect to win any prizes, but that wasn't why I wanted to give it a go.



## First Pass

The first approach was pretty simple, keep looping while there are bytes left to read and use `stream_get_line` to
extract parts of each line. It was immediately obvious that parsing the date and the URL with the likes of `parse_url`
or `DateTime` wasn't required and would add a lot of overhead.

```php
public function parse(string $inputPath, string $outputPath): void
{
    $fp = fopen($inputPath, 'r');
    $bytes = filesize($inputPath);

    $data = [];

    while (ftell($fp) < $bytes) {
        fseek($fp, strlen('https://stitcher.io'), SEEK_CUR);
        $path = stream_get_line($fp, 1000, ",");
        $date = stream_get_line($fp, 1000, "T");
        stream_get_line($fp, 1000, "\n");

        $data[$path][$date] ??= 0;
        $data[$path][$date]++;
    }

    foreach ($data as &$values) {
        ksort($values);
    }

    file_put_contents($outputPath, json_encode($data, JSON_PRETTY_PRINT));
}
```



## Inlining Static `strlen` Calls

I thought that PHP inlined calls to strlen with static strings, but it appeared to be faster to just hardcode the number.
Probably because Opcache is disabled on the CLI?

Before:
```php
fseek($fp, strlen('https://stitcher.io'), SEEK_CUR);
```

After:
```php
fseek($fp, 19, SEEK_CUR);
```



## Single line read beats multiple calls to `stream_get_line`

Turns out using string manipulation on a whole row is faster than calling `stream_get_line` multiple times to get each
part.

Before:
```php
while (ftell($fp) < $bytes) {
    fseek($fp, 19, SEEK_CUR);
    $path = stream_get_line($fp, 1000, ",");
    $date = stream_get_line($fp, 1000, "T");
    stream_get_line($fp, 1000, "\n");
    
    //...
}
```

After:
```php
while (($line = stream_get_line($fp, 4096, "\n")) !== false) {
    $commaPos = strpos($line, ',', 19);
    $path = substr($line, 19, $commaPos - 19);
    $date = substr($line, $commaPos + 1, 10);
    
    //...
}
```



## Stream Buffer Size

I suspect this is platform-dependent, but manually setting the read buffer size increased the throughput by a significant
percentage.

```php
stream_set_read_buffer($fp, 65536);
```

I did a benchmark to find the fastest buffer size:

```php
foreach ([16384, 32768, 65536, 262144, 1048576, 4194304] as $size) {
    $start = microtime(true);
    $fp = fopen($file, 'rb');
    stream_set_read_buffer($fp, $size);
    while (!feof($fp)) {
        $data = fread($fp, $size);
    }
    fclose($fp);
    printf("Buffer %7d: %.3fs\n", $size, microtime(true) - $start);
}
```



## Time For Some Benchmarking

I did a little benchmarking at this point. I needed to know where to focus. I couldn't imagine where there was further
improvement.

```php
$fp = fopen($inputPath, 'r');
stream_set_read_buffer($fp, 65536);

$data = [];

$str = 0;
$increments = 0;
$loop = -microtime(true);
while (($line = stream_get_line($fp, 4096, "\n")) !== false) {
    $strs = -microtime(true);
    $commaPos = strpos($line, ',', 19);
    $path = substr($line, 19, $commaPos - 19);
    $date = substr($line, $commaPos + 1, 10);
    $strs += microtime(true);

    $str += $strs;

    $increment = -microtime(true);
    $data[$path][$date] ??= 0;
    $data[$path][$date]++;
    $increment += microtime(true);

    $increments += $increment;
}

$loop += microtime(true);

$sort = -microtime(true);
foreach ($data as &$values) {
    ksort($values);
}
$sort += microtime(true);

$write = -microtime(true);
file_put_contents($outputPath, json_encode($data, JSON_PRETTY_PRINT));
$write += microtime(true);

var_dump($loop, $str, $increments, $sort, $write);
```

```
float(7.383) // loop - obviously this includes all the benchmarking ops in the loop, without those it's more ~5.5s
float(1.347) // string ops
float(4.276) // increment ops
float(0.210) // sort
float(0.080) // write
```



## `isset()` Beats `??=`

The majority of the time was being spent on this block:

```php
$data[$path][$date] ??= 0;
$data[$path][$date]++;
```

Thinking about it, this is shorthand for three separate operations: `isset`, assign variable and increment:

```php
if (isset($data[$path][$date])) {
    $data[$path][$date] = 0;
}
$data[$path][$date]++;
```

This can easily be reduced to two operations:

```php
if (isset($data[$path][$date])) {
    $data[$path][$date]++;
} else {
    $data[$path][$date] = 1;
}
```



## Nested Array Lookups Are Expensive

This one was a surprise. The benchmarks now showed that `isset($data[$path][$date])` was the bottleneck.

Turns out that querying nested arrays is an expensive operation. It is much faster to create a single-dimensional array
and re-assemble into a multidimensional array later.


```php
$fp = fopen($inputPath, 'r');
stream_set_read_buffer($fp, 65536);

$data = [];

while (($line = stream_get_line($fp, 4096, "\n")) !== false) {
    $commaPos = strpos($line, ',', 19);
    $key = substr($line, 19, $commaPos + 11 - 19);

    if (isset($data[$key])) {
        $data[$key]++;
    } else {
        $data[$key] = 1;
    }
}

$nestedData = [];
foreach ($data as $key => $count) {
    [$path, $date] = explode(',', $key, 2);
    $nestedData[$path][$date] = $count;
}

foreach ($nestedData as &$values) {
    ksort($values);
}

file_put_contents($outputPath, json_encode($nestedData, JSON_PRETTY_PRINT));
```



## `explode` Is Slower Than `substr`

Another minor improvement was to replace `explode` with `substr` calls. We can do this because the length of the date is
fixed. I think it also helps that it eliminates two variable assignments.

Before:

```php
foreach ($data as $key => $count) {
    [$path, $date] = explode(',', $key, 2);
    $nestedData[$path][$date] = $count;
}
```

After:

```php
foreach ($data as $key => $count) {
    $nestedData[substr($key, 0, -11)][substr($key, -10)] = $count;
}
```



## Final Benchmark

```php
$fp = fopen($inputPath, 'r');
stream_set_read_buffer($fp, 65536);

$data = [];

$increments = 0;
$strs = 0;
$lineReads = 0;
$loop = -microtime(true);
while (true) {
    $lineRead = -microtime(true);
    $line = stream_get_line($fp, 4096, "\n");
    $lineRead += microtime(true);

    $lineReads += $lineRead;

    if ($line === false) {
        break;
    }

    $str = -microtime(true);
    $commaPos = strpos($line, ',', 19);
    $key = substr($line, 19, $commaPos + 11 - 19);
    $str += microtime(true);

    $strs += $str;

    $increment = -microtime(true);
    if (isset($data[$key])) {
        $data[$key]++;
    } else {
        $data[$key] = 1;
    }
    $increment += microtime(true);

    $increments += $increment;
}
$loop += microtime(true);

$nesting = -microtime(true);
$nestedData = [];
foreach ($data as $key => $count) {
    $nestedData[substr($key, 0, -11)][substr($key, -10)] = $count;
}
$nesting += microtime(true);

$sorting = -microtime(true);
foreach ($nestedData as &$values) {
    ksort($values);
}
$sorting += microtime(true);

$writing = -microtime(true);
file_put_contents($outputPath, json_encode($nestedData, JSON_PRETTY_PRINT));
$writing += microtime(true);

var_dump($loop, $lineReads, $strs, $increments, $nesting, $sorting, $writing);
```

```
float(7.213) // loop - inc benchmarking
float(1.068) // Reading line from disk
float(1.066) // Extracting date/path strings
float(3.703) // Incrementing array
float(0.133) // creating nested array
float(0.204) // sorting array
float(0.081) // writing JSON
```



## Parallel processing

I think the next big performance jump would be to use multi-threading. There are two CPUs available.

I experimented with `pcntl_fork` but the overhead of combining the results from threads was slower than a single thread.
I'm sure I'm doing something wrong here, I suspect I need more than two threads. Another time.


```php
<?php

namespace App;

use Exception;

final class Parser
{
    public function parse(string $inputPath, string $outputPath): void
    {
        echo 1;
        $fp = fopen($inputPath, 'r');
        $bytes = filesize($inputPath);

        // get midpoint
        fseek($fp, (int)($bytes / 2));
        stream_get_line($fp, 1000, "\n");
        $midPointLineStartByte = ftell($fp);
        rewind($fp);


        $childDataOutputPath = tempnam(sys_get_temp_dir(), 'parser_');


        $pid = pcntl_fork();

        if ($pid === 0) {
            $data = $this->parseChunk($inputPath, 0, $midPointLineStartByte);
            file_put_contents($childDataOutputPath, serialize($data));
            exit(0);
        } else {
            $data = $this->parseChunk($inputPath, $midPointLineStartByte, $bytes);
        }

        pcntl_waitpid($pid, $status);

        $childData = unserialize(file_get_contents($childDataOutputPath));

        foreach ($childData as $path => $dates) {
            foreach ($dates as $date => $count) {
                $data[$path][$date] ??= 0;
                $data[$path][$date] += $count;
            }
        }

        foreach ($data as &$values) {
            ksort($values);
        }

        file_put_contents($outputPath, json_encode($data, JSON_PRETTY_PRINT));
    }

    private function parseChunk(string $inputPath, int $start, int $end): array
    {
        $fp = fopen($inputPath, 'r');
        fseek($fp, $start);

        $data = [];

        while (ftell($fp) < $end) {
            fseek($fp, strlen('https://stitcher.io'), SEEK_CUR);
            $path = stream_get_line($fp, 1000, ",");
            $date = stream_get_line($fp, 1000, "T");
            stream_get_line($fp, 1000, "\n");

            $data[$path][$date] ??= 0;
            $data[$path][$date]++;
        }

        fclose($fp);

        return $data;
    }
}
```




