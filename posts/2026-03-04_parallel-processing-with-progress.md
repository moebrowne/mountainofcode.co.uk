# Parallel Processing + Progress

#parallel


There have been a number of times ([eg](/rss-in-the-wild)) when I've wanted to run a bunch of processes as fast as
possible. I needed:

- A pool of workers - probably matching CPU count
- Live progress updates - eg % remaining/rate/ETA
- To capture the process output
- Be able to resume if stopped

I'm sure I could download/compile something purpose-built, but I felt like I should be able to solve this with standard
command line tools. I also thought it would be a fun challenge 😛 

This is what I came up with:

```bash
ppp() {
    mkdir -p results;
    local worker="
        echo;
        hash=\$(echo '%' | sha256sum | head -c 16)
        [ -f \"results/\$hash\" ] && exit 0;
        (echo '%'; ($2) 2>&1) > \"results/\$hash\"
    "

    xargs --arg-file="$1" -I % --max-procs="$(nproc)" -- bash -c "${worker}" | \
        pv --progress --rate --eta --line-mode --size $(wc -l < $1) \
        > /dev/null
}
```

The usage is `ppp /path/to/file 'command %'`. Each line in `/path/to/file` is substituted in for the `%`.

For example, to curl a bunch of URLs: `ppp urls 'curl %'`



## How It Works

It uses `xargs` with `--max-procs` to spawn processes, `pv` to monitor progress and a 4-line worker script to do the
rest. The worker script is where most of the magic happens:

```bash
# Write a newline. This gets piped to pv which updates the progress bar
echo;

# Create a short hash of the line to process
hash=\$(echo '%' | sha256sum | head -c 16)

# Check if the line has already been processed
[ -f \"results/\$hash\" ] && exit 0;

# Execute the command and write the output to a unique file
(echo '%'; ($2) 2>&1) > \"results/\$hash\"
```



## Result Files

The output of each process is written to a dedicated file: `results/{HASH}`. Where `{text}{HASH}` is the hash of the
line in the source file. Both stdout and stderr are redirected.

Additionally, the first line of each result file is the line which was processed. This is to make it possible to find
the result of a specific process via grep or similar.



## Dynamic Worker Count

The number of workers can be adjusted while the process is running by sending signals to the xargs process:

```bash
# More workers
kill -s SIGUSR1 <XARGS_PID>

# Less workers
kill -s SIGUSR2 <XARGS_PID>
```

By default, it runs one worker per CPU. For IO bound tasks adding more can speed things up considerably
