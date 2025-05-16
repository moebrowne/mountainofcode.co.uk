# Simple HTTP Router

#HTTP
#PHP

There are plenty of micro frameworks and packages which enable routing of HTTP requests to controllers. I wanted to see
what a minimal router would look like using modern PHP.

This is what I came up with:

```php
match($_SERVER['REQUEST_URI']) {
    '/' => require '/views/home.php',
    '/about' => require '/views/about.php',
    default => require '/views/404.php',
}
```

I can't imagine how it could be made any simpler; paths are mapped to PHP files. The PHP files are effectively the
controller, and if you want to keep super simple, the view can be embedded into the same file. I think people have
forgotten that PHP was primarily a templating language.



## Catch-all Routes

The `default` match arm is ideal if you need a catch-all route for dynamic paths: 

```php
match($_SERVER['REQUEST_URI']) {
    // ... other routes
    default => require 'page.php',
}
```

`page.php` might look something like this:

```php
try {
    $page = $database->findPageByPath($_SERVER['REQUEST_URI'])
} catch (NotFoundException) {
    require '/views/404.php';
    exit;
}

?>

<h1><?= $page->title ?></h1>
```



## Redirects

Adding redirects is as simple as returning a header rather than requiring a file:

```php
match($_SERVER['REQUEST_URI']) {
    '/old-page' => header('Location: /new-page', response_code: 308),
}
```



## Pattern Matching

Regex matching is possible, but I'll admit this is starting to get pretty ugly:

```php
match(true) {
    $_SERVER['REQUEST_URI'] === '/' => require '/views/home.php',
    preg_match('@^/page/(.+)$@', $_SERVER['REQUEST_URI'], $matches) === 1 => require '/views/page.php',
}
```

The `{php}$matches` array can then be accessed in `/views/page.php` to get the details of which page was requested
