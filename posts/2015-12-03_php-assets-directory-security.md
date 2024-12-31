# Disallow Execution Of PHP Scripts

#apache
#htaccess
#php
#security
#server

A lot of PHP applications that I've worked on that allow file uploads place the files into a directory that is publicly 
accessible, this isn't a problem so long as your upload script never ever allows scripts to be uploaded.

It doesn't matter how good you think your MIME type or extension filtering is why allow the PHP interpreter near the 
files you never expect to be interpreted?

## Disabling The PHP Interpreter

There is a `php_engine` directive that Apache exposes, it can be used in both `.htaccess` files and the main Apache 
config, this directive tells the PHP interpreter to just not run.

Usefully this can be placed in a `Directory` or `DirectoryMatch` block. This effectively allows us to sandboxing a 
directory, `uploads` for example.

## Disabling File Handlers

Next we tell Apache to handle all files the same, as though they were static content that doesn't need interpreting.

```bash
SetHandler none
SetHandler default-handler
RemoveHandler .cgi .php .php3 .php4 .php5 .phtml .pl .py .pyc .pyo
RemoveType .cgi .php .php3 .php4 .php5 .phtml .pl .py .pyc .pyo
```

## Disabling Overrides

As these settings can be defined in an `.htaccess` we also prevent Apache reading any `.htaccess` files in the directory
we are sandboxing. This prevents malicious `.htaccess` files disabling all protection we've added.

This is easily done by setting the `AllowOverride` directive to `None`.

## Put It All Together

```bash
<DirectoryMatch ^/path/to/(one|many)/directories>
    AllowOverride None

    SetHandler none
    SetHandler default-handler

    Options -ExecCGI
    php_flag engine off
    RemoveHandler .cgi .php .php3 .php4 .php5 .phtml .pl .py .pyc .pyo
    RemoveType .cgi .php .php3 .php4 .php5 .phtml .pl .py .pyc .pyo
    <Files *>
        SetHandler none
        SetHandler default-handler

        Options -ExecCGI
        php_flag engine off
        RemoveHandler .cgi .php .php3 .php4 .php5 .phtml .pl .py .pyc .pyo
        RemoveType .cgi .php .php3 .php4 .php5 .phtml .pl .py .pyc .pyo
    </Files>
</DirectoryMatch>
```
