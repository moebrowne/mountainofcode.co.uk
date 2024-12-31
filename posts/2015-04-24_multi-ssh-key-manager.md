# Multi-SSH Key Manager

#automation
#bash
#shell
#ssh

I use SSH literally every single day, at work and at home, so for security and because I don't want to spend time typing
long secure passwords I use SSH keys for authentication.

## What's the problem?

Usually you'll generate a key pair with `ssh-keygen`, copy the public key to any server you want to log into and you're
done. So what's the problem with that? Well if you ever want to renew that single key, increase its length for better 
security, find out which user and server that key is authorised for etc then you are going to have to change the public 
key on each of servers you can access.

It would be much better if we had a key pair per user per server, then we can renew, change or delete a key for a single
login. We have complete control.

## Key Pair Per User Per Server

You could generate a new key for each login but then when we run ssh we have to pass it the path to the key file for that
login `ssh -i /path/to/key` which is inconvenient...

You can set up the SSH config file, located at `~/.ssh/config`, with an entry for each of the servers we want to SSH into,
like this:

```
HostName serveraddress
User username
IdentityFile /path/to/key
```

That's better, now we can specify a key per login but it still requires us to add a new entry to the config file every 
time we add a new login. Fortunately for us there are a number of variables that we can use in the config to make life easier.

```
%h	The hostname of the server we're trying to connect to
%r	The username of the server we're trying to connect to
```

Using these variables we can now write generic paths for keys for any login, for example:

```
Host *
IdentityFile ~/.ssh/rsa/%h/%r
```

Now if we do `ssh foo@bar` SSH will look for a key in `~/.ssh/rsa/bar/foo`. Great we can generate a key for each of our 
logins drop them into the correct directory and ssh automatically knows where the key can be found.

The only manual task left is creating the keys themselves, moving them to the correct directory and setting permissions 
and ownership.. That sounds like that could easily be automated...

## Multi SSH Key Manger

I wrote a little script that takes care of the generation, removal and just general management of a multi SSH key setup.

You can get a copy of it on my GitHub [here](https://github.com/moebrowne/multi-ssh-key-manager) or clone the repo with:

```
git clone git@github.com:moebrowne/multi-ssh-key-manager.git
```

### Adding A New Key

Adding a new key can be as simple as:

```
./ssh-manager.sh create foo@bar.com
```

This will generate a new password-less RSA 4096 bit key pair to use when SSHing into the bar.com server as the foo user.
The create method can be passed a number of flags, for example if we want to add a passphrase for the key just use the 
`--passwd` flag and you will be asked for a passphrase. Or use `--comment "Descriptive key comment"` to add a comment to
the key.

### Removing A Key

You can securely remove any keys with:

```
./ssh-manager.sh remove foo@bar.com
```

By default, the remove method will try and use the `shred` command when deleting key files but will fall back to `rm` 
if `shred` can't be found.

### Listing Keys

To list all the currently stored keys run:

```
./ssh-manager.sh list
```

This will list out all keys currently stored. It will display the length, user, server etc for each key in a colour 
coded easy to read table. 
