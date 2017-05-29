

<!-- more -->

There are a number of projects that maintain lists of domain names that are known to be associated with adware,
 malware, adverting, gambling, adult content, tracking and other unwanted content.

The usual practice is to use the systems hosts file to cause DNS requests for those domains to resolve to
 either an IP like `127.0.0.1` or `0.0.0.0` but maintaining a host file on a whole network of systems.

A much more scalable way to approach this would be to have a single DNS server that all systems use 
