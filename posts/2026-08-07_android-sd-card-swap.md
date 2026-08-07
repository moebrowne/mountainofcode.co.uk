# Android SD Card Swap

#Android

A couple of weeks back my phone (FairPhone 3) started showing 'failed to save' errors for apps which used the sd-card,
things like the Camera and Obsidian. It would just say 'Failed to save', no more information than that, great, thanks 😡
A device reboot would get it working again. What is this, Windows???

I'd recently-ish installed Shelter, and for no good reason I'd assumed that it was something to do with that. When I
went to sync [Antenna Pod](https://antennapod.org/) (highly recommend btw) it too failed, but it gave a reason:
"File system readonly". My heart sank a little. Linux OSes typically re-mount a filesystem as read-only to protect your
data when there are serious errors, AKA it's about to fail. I'd been ignoring the problem for a couple of weeks 😬

It needed replacing fast. I didn't want to faff around with a new card and setup all the apps again, re-sync, I wanted
it to be a transparent swap. It'd be easy I thought, just `dd` from one device to another, however the only sd cards I
had lying around were smaller, big enough, but smaller. I didn't want to wait for a new one to arrive.

Turns out so long as the device UUID remains the same then Android sees it as the same sd card.


This was the process:

```bash
# Copy data off the disk
rsync -avh --progress /mnt/sd/ /backup/sd/


# Find the UUID
blkid /dev/old-sdcard # /dev/old-sdcard: LABEL_FATBOOT="<LABEL>" LABEL="<LABEL>" UUID="<UUID>" BLOCK_SIZE="512" TYPE="vfat" PARTUUID="<PART_UUID>"


# Format and partition the new sdcard
fdisk /dev/new-sdcard #o -> n -> accept defaults (single partition, full disk) -> t -> c (W95 FAT32 LBA) -> w
mkfs.vfat -F 32 -n FP3 -i "<UUID>" /dev/new-sdcard # The UUID should not have any hypens in


# Verify the new SD card has the correct ID
blkid /dev/new-sdcard # /dev/new-sdcard: LABEL_FATBOOT="<LABEL>" LABEL="<LABEL>" UUID="<UUID>" BLOCK_SIZE="512" TYPE="vfat" PARTUUID="<PART_UUID>"


# Check the new filesystem
fsck.vfat -n /dev/sdY1


# Copy files back to the disk
rsync -avh --progress /backup/sd/ /mnt/new-sdcard/
```

The key part is passing the UUID, without hypens, to `mkfs.vfat`.

