# 🗑️ Securely Deleting Files In Caja

#caja
#linux
#mate
#security

Some times you want to delete a file and for it to stay deleted forever, SSH/SSL private keys, sensitive documents, old 
password databases, etc...

Anyone who has ever accidentally deleted a file or had a hard disk fail knows there are a million and one tools out 
there that will undelete and recover these files.

This is were `shred` comes in...

## Why When I Delete Am I Not Deleting?!

All the tools that are able to recover data from a hard disk are able do so because when you delete a file the sectors 
on the HDD that stored the data for that file aren't changed instead they are marked as available. This is done for 
performance reasons, it's a lot faster to mark those sectors as available than change them.

This means that so long as those newly available sectors on the disk aren't written to the data will remain on the HDD.

To make the data permanently unrecoverable, or 'securely' delete it, we need to overwrite it. There are a number of 
command line tools we can use to do this for us, for no particular reason i'll be using `shred`.

## Shred It!

The `shred` command isn't installed by default, at least not on Ubuntu 14.04, but is available from the default repos:

```shell
apt-get install shred
```

There are a few options we can pass to `shred` to tweak how hard it would be to recover the deleted data.

```
# A good set of defaults:
shred -f -u -v -z /some/sensitive/file
```

This will:

1. Set the file as read/writable if its not and we have permission to change it
2. Over write the file with random 3 times
3. Overwrite the file with zeros to hide that it's been shredded
4. Remove the file, aka mark the sectors it occupied as 'available'

And that's it, the file is gone, nobody is getting that back so be careful!

**It's worth reading the caution at the bottom of `shred`s manpage especially if you are using and `ext3` filesystem!**

## I'm Never Going To Remember That Command?

No need, I've written a caja-action that you can import and you will be able to securely delete files from the context 
menu! Aren't I nice? :)

Don't know what a caja-action is? Me neither until the other day! I should also note here that while i'm using Caja 
because im using MATE Linux this all applies to Nautilus to.

To import the caja action you will need to first install the `caja-actions` package:

```shell
apt-get install caja-actions
```

This will give us access to the `caja-actions-config-tool` command. On running this you will be presented with the GUI.

Next [download](/documents/2015/12/20/42581e60-0ce7-4ab4-ba1d-e0b4e743c9c2.desktop) the shred action and then run the Importer: `Tools > Import assistant` Once finished hit save and 
you're done!

That's it, you should see now see a shred action in the Caja context menu, although you may notice it's hidden in 
a `Caja-Actions` submenu, if like me, you don't want this it can be disabled from the `Edit > Preferences` menu, just 
uncheck the `Create a root 'Caja-Actions' menu` option

## Things Of Note

You will only be able to remove files, this will not work with directories as `shred` only affects files. This could be 
extended to recurse a directory structure however.
