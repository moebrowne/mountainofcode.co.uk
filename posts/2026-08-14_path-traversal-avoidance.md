# Defeating Path Traversal Attacks

#PHP
#security


Imagine you have an app which takes some user input which ends up in a path:

```php
$userProvidedData = $_GET['fileName'];

$path = '/path/to/safe/directory/' . $userProvidedData;
```

This is no good, if the user sends the value `../../../../etc/passwd` then the `$path` becomes `/path/to/safe/directory/../../../../etc/passwd`
aka `/etc/passwd`. This is a classic path traversal attack.

It's very simple to defeat:

```php
$userProvidedData = $_GET['fileName'];

$basePath = '/path/to/safe/directory';
$path = realpath($basePath . '/' . $userProvidedData);

if (str_starts_with($path, $basePath) === false) {
    throw new Exception();
}
```

The key is that `{php}realpath()` returns the absolute canonical path, all symlinks, extra slashes, `../`, etc are
resolved. Now we can simply check that this path starts with the expected base path, if not then it has been tampered with.

One caveat is that this doesn't prevent path extensions. Sending a value of `something/somethingelse` will be converted
into `/path/to/safe/directory/something/somethingelse` which passes the check.


