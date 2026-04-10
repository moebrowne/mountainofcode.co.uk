# TIL: Filtering The df Command

#TIL
#UNIX

I've used the `df` command thousands of times to check how full disks are. What I find annoying is that 99.999% of the
time I'm only interested in the root partition, but I have to scan through a bunch of other loopback and tmpfs devices
to find it \#firstWorldProblems.

What I discovered is that you can pass a path to filter on: `df -h /`:

```
Filesystem                 Size  Used Avail Use% Mounted on
/dev/mapper/vgubuntu-root  914G  463G  405G  54% /
```

vs

```
Filesystem                 Size  Used Avail Use% Mounted on
udev                        16G     0   16G   0% /dev
tmpfs                      3.2G  2.6M  3.2G   1% /run
/dev/mapper/vgubuntu-root  914G  463G  405G  54% /
tmpfs                       16G  747M   15G   5% /dev/shm
tmpfs                      5.0M  4.0K  5.0M   1% /run/lock
tmpfs                       16G     0   16G   0% /sys/fs/cgroup
/dev/loop1                 128K  128K     0 100% /snap/bare/5
/dev/loop4                  64M   64M     0 100% /snap/core20/2599
/dev/loop8                  68M   68M     0 100% /snap/cups/1100
/dev/loop16                 92M   92M     0 100% /snap/gtk-common-themes/1535
/dev/loop18                219M  219M     0 100% /snap/gnome-3-34-1804/90
/dev/loop17                 13M   13M     0 100% /snap/snap-store/1216
/dev/loop13                165M  165M     0 100% /snap/gnome-3-28-1804/198
/dev/loop15                165M  165M     0 100% /snap/gnome-3-28-1804/194
/dev/loop14                350M  350M     0 100% /snap/gnome-3-38-2004/140
/dev/loop12                 13M   13M     0 100% /snap/snap-store/1113
/dev/loop19                 82M   82M     0 100% /snap/gtk-common-themes/1534
/dev/loop22                517M  517M     0 100% /snap/gnome-42-2204/202
/dev/loop21                219M  219M     0 100% /snap/gnome-3-34-1804/93
/dev/loop23                 51M   51M     0 100% /snap/snapd/25202
/dev/loop24                350M  350M     0 100% /snap/gnome-3-38-2004/143
/dev/nvme0n1p2             704M  210M  443M  33% /boot
/dev/nvme0n1p1             511M  6.1M  505M   2% /boot/efi
tmpfs                      3.2G   20K  3.2G   1% /run/user/125
tmpfs                      3.2G  208K  3.2G   1% /run/user/1000
/dev/loop25                 56M   56M     0 100% /snap/core18/2947
/dev/loop5                  74M   74M     0 100% /snap/core22/2133
/dev/loop2                  56M   56M     0 100% /snap/core18/2952
/dev/loop3                 517M  517M     0 100% /snap/gnome-42-2204/226
/dev/loop0                  64M   64M     0 100% /snap/core20/2669
/dev/loop6                  48M   48M     0 100% /snap/cups/1112
/dev/loop10                186M  186M     0 100% /snap/chromium/3259
/dev/loop9                 186M  186M     0 100% /snap/chromium/3265
/dev/loop11                 74M   74M     0 100% /snap/core22/2139
/dev/loop7                  51M   51M     0 100% /snap/snapd/25577
```