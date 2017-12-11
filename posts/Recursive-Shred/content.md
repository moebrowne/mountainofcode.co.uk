I have [previously](/2015/10/19/Securely-Deleting-Files-In-Caja/) setup an easy way to permanently
delete files from within Caja but the only downside was that it couldn't delete recursively, which
meant often manually selecting a number of files which was a pain.

This project fixes that.

<!-- more -->

## Safely Testing A Permanently Deletion Script

How do you safely test a script that's purpose is to remove files forever? I started by not calling
`shred` at all and replaced it with `echo` and `ls` this only took me so far. I needed to test the
real thing and this is where Docker stepped in. Running a test container is so easy:

```
docker run -it --rm -v /project/path/recursive-shred/:/tmp/project ubuntu:xenial bash
```

Docker allows complete separation so even if you accidentally start shredding `/` all that will
be lost is the container and they are disposable.

I also added a condition that protects the user from themselves by preventing the shredding of `/`
In the same way that `rm` wont delete `/`

## Generating Test Data

I needed files and directories to test the script on and I quickly grew tired of manually running
`mkdir` and `touch` so used bash expansion to quickly generate a whole tree structure:

```
mkdir -p /tmp/data/{alpha/{alpha/,bravo/,charlie/},bravo/{,bravo/,charlie/},charlie/{alpha/,bravo/,charlie/}}
touch /tmp/data/{{1000..1100},alpha/{{a..d}{g..k}{l..p},alpha/{a..z},bravo/{a..z},charlie/{a..d}{g..k}{l..p}},bravo/{,bravo/,charlie/},charlie/{{1100..1110},alpha/{2550..2590},bravo/,charlie/{a..d}{g..k}{l..p}}}
```


## Approach

I started with `find` to get the files and embedded it in a simple for loop:

```
for file in $(find "$directory" -type f); do
    echo "shredding $file"
done
```

While this worked I quickly remembered that what I really should do is just pipe `find`s output
into `xargs`:

```
find "$directory" -type f -print | xargs -I {} echo "shredding {}"
```
