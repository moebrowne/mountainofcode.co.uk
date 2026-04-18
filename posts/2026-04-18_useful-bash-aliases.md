# Useful Bash Aliases

#BASH

These are some of the useful Bash aliases/functions which I use most days.


## Repeat

`{bash}repeat <count|inf> <command> [args...]`

Run a command n times. If the command fails, give up. n can be a number or `inf` to repeat forever.
If the command fails more than n times, the commands' exit code is returned.

```bash
repeat() {
    if [ "$#" -lt 2 ]; then
        echo "Usage: repeat <count|inf> <command> [args...]"
        return 1
    fi

    count=$1
    shift

    if [ "$count" = "inf" ]; then
        while true; do
            "$@" || return $?
        done
    else
        for ((i=0; i<count; i++)); do
            "$@" || return $?
        done
    fi
}
```



## Retry

Keep trying to run a command until it succeeds. n sets the number of tries, set to `inf` to try forever.
The function returns the exit code from the last attempt.

`{bash}retry <maxTries|inf> <command> [args...]`

```bash
retry() {
    if [ "$#" -lt 2 ]; then
        echo "Usage: retry <maxTries|inf> <command> [args...]"
        return 1
    fi

    maxTries=$1
    shift

    if [ "$maxTries" = "inf" ]; then
        while true; do
            "$@" && return $?
        done
    else
        for ((i=0; i<maxTries; i++)); do
            "$@" && return $?
        done
    fi
}
```



## Forever

Keep running command even if it fails.

`{bash}forever <command>`

```bash
forever() {
    while true; do
        "$@" || :
    done
}
```

With exponential back-off:

```bash
forever() {
    local delay=1
    local maxDelay=300  # 5 minutes max
    local backoffFactor=2

    while true; do
        if "$@"; then
            # Reset delay on success
            delay=1
        else
            echo "Retrying in ${delay}s..." >&2
            sleep "$delay"

            delay=$((delay * backoffFactor))
            if [ "$delay" -gt "$maxDelay" ]; then
                delay=$maxDelay
            fi
        fi
    done
}
```



## Strip Exif Data From Images

This leans on Imagick's `convert` binary to remove all [Exif](https://en.wikipedia.org/wiki/Exif) data from an image.

```bash
alias strip-exif='convert $1 -strip $1'
```



## Start A Web Server

Starts a PHP webserver in the current working directory. The port can be specified, if omitted, it will bind to a random,
available port.

```bash
phps() {
  local port="${1:-0}"
  php -S 127.0.0.1:$port
}
```
