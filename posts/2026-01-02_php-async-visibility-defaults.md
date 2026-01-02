# PHP Asymmetric Visibility Cheat Sheet

#PHP

PHP 8.4 added support for property [asymmetric visibility](https://wiki.php.net/rfc/asymmetric-visibility-v2). This feature allows controlling get/set access
separately. It includes a bunch of shorthand and implicit declarations which make it hard to remember.

Below is my attempt to create a cheat-sheet which documents all the possibilities.

| Declaration              | Get       | Set       | Effective Definition                |
|--------------------------|-----------|-----------|-------------------------------------|
| public                   | public    | public    | public public(set)                  |
| protected                | protected | protected | protected protected(set)            |
| private                  | private   | private   | final private private(set)          |
| public(set)              | public    | public    | public public(set)                  |
| protected(set)           | public    | protected | protected protected(set)            |
| private(set)             | public    | private   | final private private(set)          |
| public public(set)       | public    | public    | public public(set)                  |
| public protected(set)    | public    | protected | public protected(set)               |
| public private(set)      | public    | private   | final public private(set)           |
| protected public(set)    | invalid   | invalid   |                                     |
| protected protected(set) | protected | protected |                                     |
| protected private(set)   | protected | private   | final protected private(set)        |
| private public(set)      | invalid   | invalid   |                                     |
| private protected(set)   | invalid   | invalid   |                                     |
| private private(set)     | private   | private   | final private private(set)          |
| public readonly          | public    | protected | public protected(set) readonly      |
| protected readonly       | protected | protected | protected protected(set) readonly   |
| private readonly         | private   | private   | final private private(set) readonly |
