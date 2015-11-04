title: "Lets Encrypt Certificate"
tags:
- server
- Apache
- HTTP
- SSL
- Security
author: Oliver
photos:
- /images/Lets-Encrypt.png
---

A little while back I signed up for the [Lets Encrypt](https://github.com/letsencrypt) beta for this domain, recieved an email today confirming that I had been invited to join the beta! And that I could generate a free SSL cert!

So I immediately did!

<!-- more -->

## It's Too Easy!

The process was super simple and the 3 commands to run were included in the email:

```bash
# Clone the repo
git clone https://github.com/letsencrypt/letsencrypt

# Change Directory
cd letsencrypt

# Run the script
./letsencrypt-auto --agree-dev-preview --server https://acme-v01.api.letsencrypt.org/directory certonly
```

Easy as that. This launched an interactive `dialog` interface that asked a couple of simple questions like which domains I wanted to generate certs for and that was it I was left with a new Apache vhost config in the `sites-avaliable` directory, I just simply run `a2ensite mountainofcode.co.uk-le-ssl.conf` and you're done, free and secure! Took less than a minute

The new VHost that was created was an exact copy of the non SSL vhost I was currently using but with the releveant SSL directives `SSLCertificateFile` and `SSLCertificateKeyFile` added and also an `Include` for some SSL related settings, although I did have to add the paths to the actual cert files my self but this was trivial.

One thing I did have to do myself was add the `SSLCertificateChainFile` directive as Firefox was complaining that the certificate was untrusted, again easy enough to do, don't know if this is due to the beta?

## SSL All The Things

I'm really hoping to see a massive rise in the number of sites using SSL certs when the Lets Encrypt SSL certs are made generally avalaible on the [week beginning 16 Nov 2015](https://letsencrypt.org/2015/08/07/updated-lets-encrypt-launch-schedule.html) esspecially from smaller sites, as there is litteraly no barrier to entry any more, no cost, and minimal effort required from the server admin.
