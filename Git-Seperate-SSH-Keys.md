title: "Adding SSH Key Awareness To Git Repos"
tags:
- shell
- bash
- Git
- SSH
author: Oliver
---

I maintain and work on a number of repos on BitBucket for both work and in my own time and use a separate account for
each. As SSH is used to communicate I use my [Multi SSH Key Manager]() project to manage the keys.

The problem with this is that the remotes for all BitBucket repos have the same username and server `git@bitbucket.org`
and as soon as I associate my key for `git@bitbucket.org` with one account I can't associate it with another.

I could link the accounts together and then they could both use the same key but I want to keep them separate, so I
needed to find a way of telling Git to use a certain key with a certain repository.

Here's how I did it...

<!-- more -->



##  Notes

- Need certain version of Git >= v2.3
- Cant use SSH config as the addresses are the same git@github/bitbucket
- Want to keep GitHub and BitBucket accounts separate
+ Have to use separate keys for each account
+ Don't want to link accounts
+ Want to develop on both accounts from a single machine
- Could add another key to the .ssh dir but Multi SSH key setup wont allow as SSH non recursive
- uses `git config -l` so config cascades from global to repo
- future dev:
  - understand different remotes
  - whole profiles



