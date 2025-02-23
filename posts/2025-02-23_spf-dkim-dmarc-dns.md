# SPF, DKIM & DMARC DNS

#DNS
#Email
#Security

Some notes on where email security DNS records are kept.

```
dig example.org TXT #SPF
dig _dmarc.example.org TXT #DMARC
dig {SELECTOR}._domainkey.example.org TXT #DKIM
```

Where does the DKIM `{text}{SELECTOR}` come from? It's vendor specific; for GMail it's `google`, for Mailgun it's `k1`,
etc. There are a few lists of selectors on the internet:

- [https://github.com/zkemail/registry.prove.email/files/14850741/selector_frequencies.txt](https://github.com/zkemail/registry.prove.email/files/14850741/selector_frequencies.txt)
- [https://topdeliverability.com/email-service-providers-handbook/](https://topdeliverability.com/email-service-providers-handbook/)
- [https://github.com/ryancdotorg/dkimscan/blob/master/dkimscan.pl](https://github.com/ryancdotorg/dkimscan/blob/2d0d0d73008c09948b751fba097104f41d3b77f5/dkimscan.pl#L313-L451)
