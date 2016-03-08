title: "Apache Hostname Header"
tags:
- Apache
- HTTP
- Headers
- Hostname
author: Oliver
photos:
- /images/some-image
---

I was recently involved in setting up a complex load balanced Auto-scaling multi server setup and to make life easy wanted set a header that contained the servers hostname so I knew which server behind the load balancer satisfied each request.

I thought this would be easy... Nope! But I managed it and here's how... 

<!-- more -->

## The Options

There are 2 options and the one you want depends on which version of Apache you're using. You're more than likely using 2.4.7 as that's the version that ships with Ubuntu 14.04 LTS.

If you are using <2.4.10 you will need to setup an environment variable as described next, other wise you can use the more elegant expression method, see even further below for that one

## Environment Variables

There is an `envvars` file, on Ubuntu in `/etc/apache2/envvars`, that defines a number of environment variables Apache uses for various things, we can define a new variable in here and set it to the servers hostname by adding the following line

```bash
export HOSTNAME=$(hostname);
```

This sets a new variable we can use in our Apache configs by using the `${HOSTNAME}` syntax.

## Header Expressions

As mentioned above if you are using Apache 2.4.10 or later then life becomes a lot easier as you can pass an expression to the value portion of the `Header` directive.

The expressions we can pass include a bunch of functions we can call (they're listed [here](https://httpd.apache.org/docs/2.4/expr.html#functions)). We're interested in the `file` function as we can use this to read the contents of the `/etc/hostname` file that contains our hostname.

So you will end up with something that looks like this:

```
Header set X-Host "expr=%{file:/etc/hostname}"
```

After a reload you should see your `X-Host` header appear. It seems the file is read on every request which would be really useful if the file was changing but I don't know about you but I don't tend to change my hostname all that often so it may add a little unnecessary overhead.


