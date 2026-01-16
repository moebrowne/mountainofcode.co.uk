# Does Anyone Require PHP Extensions By Version?

#PHP
#Composer


Most non-trivial PHP projects will use [Composer](https://getcomposer.org/) to pull in dependencies. The dependencies
are almost always PHP libraries, but they can be PHP extensions. Something like this in `composer.json`:

```json
{
    "require": {
        "ext-mbstring": "*"
    }
}
```

The `*` means any version. I don't think I've ever seen a specific version of an extension requried, it's always `*`. I
thought maybe it was just that the type of projects I work on didn't require it. Perhaps it's more of a framework or 
library thing? I thought it might be interesting to find out.

I used [nikic/popular-package-analysis](https://github.com/nikic/popular-package-analysis) to download the top 1000
Composer packages and threw together a quick script to parse all extension dependencies:

```php
<?php

$extensionUsage = [];

foreach(glob(__DIR__ . '/sources/*/*/composer.json') as $path) {
    $composerData = json_decode(file_get_contents($path), true);

    $extensions = [];

    foreach ($composerData['require'] ?? [] as $package => $version) {
        if (str_starts_with($package, 'ext-') === false || $version === '*') {
            continue;
        }
        echo $composerData['name'] . ' => ' . $package . ' (' . $version . ')' . PHP_EOL;

        if (isset($extensionUsage[$package][$version]) === false) {
            $extensionUsage[$package][$version] = 0;
        }

        $extensionUsage[$package][$version]++;
    }

    foreach ($composerData['require-dev'] ?? [] as $package => $version) {
        if (str_starts_with($package, 'ext-') === false || $version === '*') {
            continue;
        }

        echo $composerData['name'] . ' [dev] => ' . $package . ' (' . $version . ')' . PHP_EOL;

        if (isset($extensionUsage[$package][$version]) === false) {
            $extensionUsage[$package][$version] = 0;
        }

        $extensionUsage[$package][$version]++;
    }
}
```

This returned only 10 packages:

```
api-platform/core [dev]                     => ext-mongodb (^1.21 || ^2.0)
composer/package-versions-deprecated [dev]  => ext-zip (^1.13)
doctrine/mongodb-odm-bundle                 => ext-mongodb (^1.21 || ^2)
doctrine/mongodb-odm                        => ext-mongodb (^2.1)
hollodotme/fast-cgi-client [dev]            => ext-xdebug (>=2.6.0)
league/flysystem [dev]                      => ext-mongodb (^1.3|^2)
mongodb/laravel-mongodb                     => ext-mongodb (^1.21|^2)
mongodb/mongodb                             => ext-mongodb (^2.1)
opensearch-project/opensearch-php           => ext-json (>=1.3.7)
spatie/laravel-backup                       => ext-zip (^1.14.0)
```

