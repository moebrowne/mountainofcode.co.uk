title: "Adding SSH Key Awareness To Git Repos"
tags:
- shell
- bash
- Git
- SSH
author: Oliver
---

I maintain and work on a number of repositories on BitBucket for both work and in my own time and use a separate account for
each. As SSH is used to communicate I use my [Multi SSH Key Manager]() project to manage the keys.

The problem with this is that the remotes for all BitBucket repos have the same username and server `git@bitbucket.org`
and as soon as I associate my key for `git@bitbucket.org` with one account I can't associate it with another.

I could link the accounts together and then they could both use the same key but I want to keep them separate, so I
needed to find a way of telling Git to use a certain key with a certain repository.

Here's how I did it...

<!-- more -->

## How It Works

The way it works is that all git commands are intercepted with an alias that looks for the config variable `SSH.keypath`
using the `git config -l` command and if the config has an entry it exports the value of the config entry to an
environment variable named `GIT_SSH_COMMAND`.

Git uses this environment variable when it calls SSH this allows us pass a path to the key to SSH using the `-i` flag.

## Why Can't You Just Add Another Key To Your .ssh Directory?

Good question. I could just generate a new key and stick it along side my current `id_rsa` key but I am using an SSH key
manager that I wrote a little while back to help keep keys all separated and all the keys generated are in a
subdirectory and doesn't look recursively when trying to find the right key to use.

## Requirements

The `GIT_SSH_COMMAND` environment variable was only added in Git v2.3 so can only be used in v2.3 and above

## Cascading Config

I used `git config -l` to check for the existence of the config variable and the nice thing about this is that this
checks not only the config of the current repo but the global Git config to so you could for example define a default
key globally and override it where you need to

## Limitations

Currently you can only define one key per repository and that one will be used when communicating with all the remotes,
i'd like to give have more granular control so I can give a list of remotes or remote names and specify a key for each
and a default to fall back to.

##  Notes

+ Need certain version of Git >= v2.3
+ Cant use SSH config as the addresses are the same git@github/bitbucket
+ Want to keep GitHub and BitBucket accounts separate
+ Have to use separate keys for each account
+ Don't want to link accounts
+ Want to develop on both accounts from a single machine
+ Could add another key to the .ssh dir but Multi SSH key setup wont allow as SSH non recursive
+ uses `git config -l` so config cascades from global to repo
+ future dev:
  + understand different remotes
  - whole profiles



