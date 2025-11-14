# Blocking Vulnerable Packages With Composer

#composer
#PHP
#security

![](/images/composer.png)

Composer is the goto way of installing third party code into PHP projects. It's also a great way to add vulnerable code
to your project. To be clear, this isn't a dig at Composer; all package managers have the same problem. What are the
simple but effective tools we have to prevent vulnerable code from being included in our projects?


## The Audit Command

The `composer audit` command checks if any of your installed packages have known vulnerabilities. This can help you
check you aren't shipping vulnerable to production, it often appears in CI pipelines.


## A Meta Package

[roave/security-advisories](https://github.com/Roave/SecurityAdvisories) is a meta-package which contains a huge list of
conflicts for vulnerable versions of packages. Its benefits are that it prevents installing vulnerable packages in the
first place and doesn't rely on remembering to run an extra command.


## Composer 2.9

[Composer 2.9](https://blog.packagist.com/composer-2-9/) introduced [`config.audit.block-insecure`](https://getcomposer.org/doc/06-config.md#block-insecure).
This prevents Composer from installing vulnerable packages, effectively making the roave/security-advisories package
redundant. It is set to true by default.


### Handling Benign Vulnerabilities

What happens when a vulnerability is discovered, but it doesn't affect your app because it [requires some specific config](https://github.com/advisories/GHSA-gv7v-rgg6-548h),
or only affects Windows OS? You obviously upgrade if you can, but that's not always viable due to compatibility or time
constraints.

The answer is to add the vulnerability ID to your projects composer.json 

> A list of advisory ids, remote ids or CVE ids that are reported but let the audit command pass.

```json
{
    "config": {
        "audit": {
            "ignore": {
                "CVE-2024-52301": "Non-issue due to register_argc_argv being set to off."
            }
        }
    }
}
```

See the [docs](https://getcomposer.org/doc/06-config.md#ignore) for more details.

To whom ever required that you have to give a reason why it's ignored: thank you.
