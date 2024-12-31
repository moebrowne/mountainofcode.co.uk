# Capturing A Whole HTTP Request With PHP

#http
#php

I was doing some forensic analysis of PHP scripts on a server a while ago and wanted to capture all parts of the HTTP 
requests these scripts were receiving. I was surprised to find no one else had done this, or at least hadn't shared it, 
so I wrote my own...

## The Script

The script below when placed at the head of or in place of the script you want to capture requests to will write all 
data sent to files in a directory of your choosing for later analysis:

```php
$dir = "/tmp/analysis/a-descriptive-directory-name/".date("Y/m/d/Hi.s");
mkdir($dir, 0700, true);

file_put_contents($dir."/_REQUEST", var_export($_REQUEST,true));
file_put_contents($dir."/_COOKIE", var_export($_COOKIE,true));
file_put_contents($dir."/_SERVER", var_export($_SERVER,true));
file_put_contents($dir."/_POST", var_export($_POST,true));
file_put_contents($dir."/_GET", var_export($_GET,true));
file_put_contents($dir."/_FILES", var_export($_FILES,true));

$data = file_get_contents('php://input');

file_put_contents($dir."/input", var_export($data,true));
```

Nothing complicated but hopefully it will save someone some time. I also made it available as a [Gist](https://gist.github.com/moebrowne/a780716832686819d557)