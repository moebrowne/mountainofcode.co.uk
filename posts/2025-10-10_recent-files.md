# Recent Files

In all the file browsers I've used across various flavours of Ubuntu there has been a 'Recent files' tab, it's useful
to quickly get at a file I just downloaded or saved from another application.

I have found times when I've expected an entry to show in the recent file list, but it wasn't there, taking a screenshot
for example. I wondered if there was a way of adding entries directly, where is the list stored? A few searches later
led to: `.local/share/recently-used.xbel`

There doesn't appear to be any official CLI tools to work with it. I did find [xenomachina/recently_used](https://github.com/xenomachina/recently_used/blob/master/recently_used.py),
but I thought I could make something much simpler; all I needed to do is write some XML to a file. 

Existing entries in the file showed a bunch of metadata, I took an entry and whittled it down to the bare minimum that
still showed an entry in the list, this is what I got:

```xml
<bookmark
    href="file:///path/to/file"
    added="2025-09-20T12:40:59Z"
    modified="2025-09-20T12:40:59Z"
    visited="2025-09-20T12:40:59Z"
/>
```

Next, I created a bash function which updates the files:

```bash
addRecent() {
    set -euo pipefail

    recentFilesDbPath="${XDG_DATA_HOME:-$HOME/.local/share}/recently-used.xbel"
    filePath="${1}"
    timestamp=$(date -u +"%Y-%m-%dT%H:%M:%SZ")

    if [[ -z "$filePath" ]]; then
      echo "Usage: $(basename "$0") /path/to/file" >&2
      exit 1
    fi

    if [[ ! -f "${filePath}"]]; then
      echo "Given file does not exist: ${filePath}"
      exit 2
    fi

    if [[ -f "$recent" ]]; then
        cat <<'EOF' > test
<?xml version="1.0" encoding="UTF-8"?>
<xbel version="1.0" xmlns:bookmark="http://www.freedesktop.org/standards/desktop-bookmarks" xmlns:mime="http://www.freedesktop.org/standards/shared-mime-info">
</xbel>
EOF
    fi

    bookmarkEntry="<bookmark href=\"file://$(realpath "${filePath}")\" added=\"${timestamp}\" modified=\"${timestamp}\" visited=\"${timestamp}\"/>"

    sed -i "s|</xbel>|${bookmarkEntry}\n</xbel>|" "${recentFilesDbPath}"
}
```


If you're not worried about the edge cases, it's even easier:

```bash
addRecent() {
    set -euo pipefail

    timestamp=$(date -u +"%Y-%m-%dT%H:%M:%SZ")
    bookmarkEntry="<bookmark href=\"file://$(realpath "${1}")\" added=\"${timestamp}\" modified=\"${timestamp}\" visited=\"${timestamp}\"/>"

    sed -i "s|</xbel>|${bookmarkEntry}\n</xbel>|" "${XDG_DATA_HOME:-$HOME/.local/share}/recently-used.xbel"
}
```

