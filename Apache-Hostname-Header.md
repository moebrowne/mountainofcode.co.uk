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

I was recently involved in setting up a complex load balanced Auto-scaling multi server setup and to make life easy wanted set a header that contained the servers hostname so I knew who behind the load balancer satisfied each request.

I thought this would be easy... Nope! But I managed it and here's how... 

<!-- more -->

## Environment Variables

There is an `envvars` file, on Ubuntu in `/etc/apache2/envvars`, that defines a number of environment variables Apache uses for various things, we can define a new variable in here and set it to the servers hostname by adding the following line

```bash
export HOSTNAME=$(hostname);
```

This sets a new variable we can use in our Apache configs by using the `${HOSTNAME}` syntax.
