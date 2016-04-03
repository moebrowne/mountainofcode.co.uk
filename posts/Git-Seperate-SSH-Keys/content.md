I maintain and work on a number of repositories on BitBucket for both work and in my own time and use a separate account for each. SSH is used to talk with the remotes and I use my [Multi SSH Key Manager](/2015/04/24/Multi-SSH-Key-Manager/) to manage the keys.

The problem with this is that the remotes for all BitBucket repos have the same username and server `git@bitbucket.org` and as soon as I associate a key for `git@bitbucket.org` with my work account, I can't associate it with my personal account.

I could link the accounts together and then they could both use the same key but I want to keep them separate, so I needed to find a way of telling Git to use a certain key with a certain remote.

Here's how I did it...

<!-- more -->

## How It Works

It was possible to solve this problem only because Git looks at the value of an environment variable called `GIT_SSH_COMMAND` when it calls SSH (see Requirements below). The way it works is that all calls to Git are intercepted with an alias, this alias then looks for the config variable `SSH.keypath` by calling `git config -l` and if the config variable has been defined we prefix the git command with the `GIT_SSH_COMMAND` set to `ssh -i <SSH.keypath value>`.

## Why Can't You Just Add Another Key To Your .ssh Directory?

Good question. I could just generate a new key and stick it along side my default key but I am using my [Multi SSH Key Manager](/2015/04/24/Multi-SSH-Key-Manager/) that I wrote a little while back to help keep keys all separated and all the keys generated are in a subdirectory and SSH won't look recursively when trying to find keys to use.

## Requirements

The `GIT_SSH_COMMAND` environment variable was only added in Git v2.3 so can only be used in v2.3 and above.

## Cascading Config

I used `git config -l` to check for the existence of the config variable and the nice thing about this is that `git config` checks not only the config of the current repo but also the global Git config to, so you could for example define a default key in your global Git config (in `~/.gitconfig`) and then override it where you need to

## Limitations

Currently you can only define one key per repository and that one will be used when communicating with all the remotes, i'd like to have more granular control so I can give a list of remotes or remote names and specify a key for each and a default to fall back on.
