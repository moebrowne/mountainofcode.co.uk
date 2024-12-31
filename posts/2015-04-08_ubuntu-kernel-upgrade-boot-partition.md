# Ubuntu Kernel Upgrade /boot Partition

#automation
#kernel
#ubuntu
#upgrade

There was a new version of the Linux kernel released today, 3.13.0-49, once again I came across the issue of my /boot partition not having enough free space to fit the new kernel and was greated by this message:

    The upgrade needs a total of XX M free space on disk /boot.
    Please free at least an additional XX M of disk space on /boot

Theres an easy if a little dangerous fix to be found on the [AskUbuntu](http://askubuntu.com/questions/298487/not-enough-free-disk-space-when-upgrading) site.

TL;DR version:

    sudo apt-get purge linux-image-3.13.0-{X,Y,Z}-generic`

Where `X` `Y` and `Z` are the versions you want to delete.

Can't help but think there is way to automate the purging of all but say the last 2 kernel versions any time a new one is released...
