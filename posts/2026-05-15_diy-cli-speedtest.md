# DIY CLI Speedtest 🏁

#CLI
#speedtest

I usually turn to [speedof.me](https://speedof.me/) to check my current internet connection speed. I thought it might be
fun to create my own CLI version, yes, I know there are already loads of existing CLI speed test tools, but I want to
reinvent this particular wheel!

I thought it must be possible to achieve by gluing together existing CLI tools rather than turning to full-blown
programming language. This is what I achieved:

```bash
speedtest() {
    local DOWNLOAD_URL="https://nbg1-speed.hetzner.com/100MB.bin"
    local DOWNLOAD_SIZE_MB=100
    local UPLOAD_URL="https://speed.cloudflare.com/__up"
    local UPLOAD_SIZE_MB=20
    
    curl --silent "${DOWNLOAD_URL}" \
        | pv --average-rate --progress --size "$(( DOWNLOAD_SIZE_MB * 1024 * 1024 ))" --name "Download" --interval 0.5 \
        > /dev/null

    dd if=/dev/urandom bs=1M count="$UPLOAD_SIZE_MB" 2>/dev/null \
        | pv --average-rate --progress --size "$(( UPLOAD_SIZE_MB * 1024 * 1024 ))" --name "Upload  " --interval 0.5 \
        | curl --silent --upload-file - "${UPLOAD_URL}"
}
```

Ideally, I would've liked to avoid using Cloudflare because I don't want to support their internet monopoly, but they
seem to be the only place on the internet which accepts random streamed uploads.

There is plenty of room for improvement, but it gives accurate enough readings for me. You may want to change the
endpoints if you are somewhere else in the world.

