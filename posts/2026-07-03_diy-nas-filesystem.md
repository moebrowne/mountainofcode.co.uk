# DIY NAS Part 3: The Filesystem

#NAS
#project


*This is part 3 of the project, see [here](/diy-nas-plan) for the other parts.*

Which filesystem to use was fundamental. It affects power, noise and resilience. I planned to use RAID to get
resilience, but typical RAID setups prevent disks from spinning down, which means more noise and power ([more than you
might think](/hdd-power-usage)).

I did a bunch of research and found that a combination of [SnapRAID](https://www.snapraid.it/) and [MergerFS](https://trapexit.github.io/mergerfs/latest/)
was going to be best for my goals. They each solved a single problem and didn't lock my data away in some obscure way.

The ability to be able to pull out a drive and stick it in another machine is invaluable when (not if) stuff goes wrong.




## SnapRAID

SnapRAID protects data from disk failure. It does this by storing parity data on a dedicated drive. It uses drives
formatted to good old boring EXT4.

I will use a single parity drive, this will protect all the data from a single drive failure. If two drives were to fail
simultaneously, I would lose only the files which are stored on the failed drives. This is unlike regular RAID where if
too many drives fail, you lose everything.



## MergerFS

MergerFS allows creating a single virtual drive which is actually many physical drives. This is a convenient abstraction
allowing me to spread data between disks without applications noticing. It does all this while still allowing normal
access to each disk, no proprietary nonsense.



## Allowing Disks To Sleep AKA Caching

SnapRAID and MergerFS cover resilience and convenience, but don't allow the disks to spin down.

I hoped to be able to use [Bcache](https://bcache.evilpiepirate.org/), it transparently routes requests for frequently
accessed data to a fast, silent, disk, but it required using a special format on all the disks, that's a no-go for me.
Instead, I can achieve the same thing with just MergerFS and 'tiered caching'.

There are lots of details in [the docs](https://trapexit.github.io/mergerfs/latest/extended_usage_patterns/#tiered-cache)
about how exactly tiered caching works. The short version is that you use an NVME drive to store 'hot' data, and only
wake the HDDs when retrieving 'cold' data. This requires a script to be run periodically to move files on the NVME which
have since gone cold or just to free up space.

This is more work than using Bcache, but it also makes a lot more sense in my setup. The NVME drive I will be using for
the cache is 4TB (it's what I've got on hand). With Bcache all that space would be dedicated to ephemeral cache. With
the MergerFS approach, that space is added to the total storage space. Win, win.

The only downside is that I protect the cache disk with SnapRAID because the parity disk has to be at least as large as
the largest disk in the array.




## Putting It All Together

Format all the drives to ext4. `-m` sets no reserved space for root, `-L` sets the label.

```
mkfs.ext4 -m 0 -L solaris /dev/nvme0n1
mkfs.ext4 -m 0 -L aquila /dev/usb-aquila
mkfs.ext4 -m 0 -L carina /dev/usb-carina
mkfs.ext4 -m 0 -L dorado /dev/usb-dorado
mkfs.ext4 -m 0 -L fornax /dev/usb-fornax
mkfs.ext4 -m 0 -L hydrus /dev/usb-hydrus
mkfs.ext4 -m 0 -L tucana /dev/usb-tucana
```

Next `fstab`. I considered using the `/dev/usb-x` or `/dev/disk/by-label/` paths, but it felt like the UUID was safest:

```
/dev/disk/by-uuid/082063a5-faaf-45b4-9545-a727d8ebceaf /media/solaris ext4 defaults,nofail,x-systemd.device-timeout=5 0 2
/dev/disk/by-uuid/492fcaac-a610-4824-b1e4-cbfdbdd7b17c /media/aquila ext4 defaults,nofail,x-systemd.device-timeout=5 0 2
/dev/disk/by-uuid/fb5dad8d-eb6a-43e6-9fe8-45edc8b0efad /media/carina ext4 defaults,nofail,x-systemd.device-timeout=5 0 2
/dev/disk/by-uuid/659b3ef9-ec03-4ecd-a1cc-7cd7ad795bca /media/dorado ext4 defaults,nofail,x-systemd.device-timeout=5 0 2
/dev/disk/by-uuid/2f700da3-b3ca-4170-9663-5682778a4cda /media/fornax ext4 defaults,nofail,x-systemd.device-timeout=5 0 2
/dev/disk/by-uuid/bc17cff4-1b5b-45d0-8b24-1c9e7f00a90e /media/hydrus ext4 defaults,nofail,x-systemd.device-timeout=5 0 2
/dev/disk/by-uuid/761a3128-a3d6-4b34-aa6e-16c600944689 /media/tucana ext4 defaults,nofail,x-systemd.device-timeout=5 0 2
```

Create mount directories:

```
sudo mkdir /media/{aquila,carina,dorado,fornax,hydrus,tucana,solaris}
```

Mount drives

```
sudo mount -a
```


Next was setting up SnapRAID. The Ubuntu global repos, of course, only had v12.1, so I compiled the v13 from source:

```
wget https://github.com/amadvance/snapraid/releases/download/v13.0/snapraid-13.0.tar.gz
tar -zxvf snapraid-13.0.tar.gz .
./configure
make
sudo make install
```

Setup the SnapRAID config:

```
parity /media/aquila/snapraid.parity

content /var/snapraid/snapraid.content
content /media/carina/snapraid.content
content /media/dorado/snapraid.content
content /media/fornax/snapraid.content
content /media/hydrus/snapraid.content
content /media/tucana/snapraid.content

data d1 /media/carina/
data d2 /media/dorado/
data d3 /media/fornax/
data d4 /media/hydrus/
data d5 /media/tucana/

smartctl d1 -d sat %s
smartctl d2 -d sat %s
smartctl d3 -d sat %s
smartctl d4 -d sat %s
smartctl d5 -d sat %s
smartctl parity -d sat %s
```


Install latest MergerFS

```
wget https://github.com/trapexit/mergerfs/releases/download/2.41.1/mergerfs_2.41.1.ubuntu-jammy_amd64.deb
sudo dpkg -i mergerfs_2.41.1.ubuntu-jammy_amd64.deb
```

Configure fstab to set up two pools. `corona` is the pool with the NVME 'cache', `glacius` is the cold storage:

```
/media/solaris:/media/carina:/media/dorado:/media/fornax:/media/hydrus:/media/tucana /media/corona fuse.mergerfs defaults,config=/etc/mergerfs/corona.conf 0 2
/media/carina:/media/dorado:/media/fornax:/media/hydrus:/media/tucana                /media/glacius fuse.mergerfs defaults,config=/etc/mergerfs/glacius.conf 0 2
```

The `corona` pool is configured to direct all reads and writes to the first (NVME) drive:

```
fsname=corona
cache.files=auto-full
category.create=ff
category.search=ff
minfreespace=50G
moveonenospc=true
func.getattr=newest
dropcacheonclose=false
```

The `glacius` pool spreads data around based on free space:

```
fsname=glacius
cache.files=auto-full
category.create=pfrd
func.getattr=newest
dropcacheonclose=false
```


`df -h` now shows

```
/dev/sdb                           916G   28K  916G   1% /media/aquila
/dev/sdc                           916G   32K  916G   1% /media/dorado
/dev/sda                           916G   32K  916G   1% /media/carina
/dev/sde                           916G   32K  916G   1% /media/hydrus
/dev/sdd                           916G   32K  916G   1% /media/fornax
/dev/sdf                           916G   32K  916G   1% /media/tucana
/dev/nvme0n1p1                     3.6T  795G  2.7T  23% /media/solaris
corona                             8.1T  795G  7.1T  10% /media/corona
glacius                            4.5T  160K  4.5T   1% /media/glacius
```

8,100 GB of glorious storage. Now to find stuff to fill it with 😅

