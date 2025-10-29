# Some Love For PHPs Dev Server

#PHP

<div style="text-align: center; padding-left: 12px; font-size: 1.8rem;">

`php -S localhost:8008`

</div>

Simple, easy to remember, always within reach ❤️

PHPs [built-in dev server](https://www.php.net/manual/en/features.commandline.webserver.php) is great, is it going to
compare against the likes of Apache and nginx? No, nor should it. It's not meant to be a production server, it's the
simplest little server which is quick and easy. I use it for all kinds of little projects, hacks, test servers, etc.

One thing that many people don't know is that since PHP 7.4 it supports multiple threads via an environment variable.
Setting the environment variable `PHP_CLI_SERVER_WORKERS=$(nproc)` will automatically create as many workers as your
machine has threads. This can speed up apps which make a lot of parallel requests.

I have set this env variable in my `.bashrc` file so that any server that gets spun up gets the benefit.

Thanks to IPv6 shorthand notation, there is an even shorter version, though arguably it's harder to type:

```
php -S [::1]:8008
```


## Update (29 Oct 25) - Dynamic Ports

Since PHP 8.0 if you use port 0 then PHP will bind to the next available port. Apparently this is a common non-standard
but common feature in software.
