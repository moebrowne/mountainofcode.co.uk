# Progress Dots

#CLI
#TUI


Creating single-purpose scripts is super useful for all kinds of things, from imports, to data manipulations, to load
testing, etc. One thing I add to all but the most basic scripts is a single-character-per-operation output, something
like this:

```terminaloutput
............................u..........................F..........FFFFF.........
```

It's more than just for style points, it's very information-dense.

The most obvious is the status. Each character represents a single operation and its outcome. For example, a data import
might use `.` for a new row, `F` for failure, `s` for skipped, `u` for a record which already exists and was updated,
etc.

I'd recommend using `.` for the default/expected outcome because it's low-contrast and makes the other characters stand
out.

Systematic errors quickly become apparent. If you see nothing but `FFFFFFFFFFFFFFFFFF` from the start, you don't need to
wait until the process finishes, you can kill the process early and investigate.

Beyond status, there is a temporal aspect. One time I had a performance bug, where each operation got slower and slower.
Running it without any output, it would be easy to assume that the process was slow on average. Seeing the characters
being printed slower and slower made it obvious.

There are other situations too, like everything progresses normally, but every 250 operations it pauses for a while.
This might indicate a reconnection, garbage collection, rate limiting, etc.

The count of each character is a quick way to get a summary, and obviously the total number of operations. The total can
be useful when debugging:

```
..................................................
Fatal error: Unexpected spline reticulation encountered.
process exited with status code 2
```

From this I know that operation 51 was where the error originated. This can be invaluable for finding the source of the
issue. Watch out for off-by-one errors, the error message will probably be printed before the status character was.

The thing I like the most about this is that it's trivial to add. A few `{php}echo` statements is all you need. There's
almost no reason not to.

As a bonus, if you know the expected number of operations, you can pipe the output into `pv` and get a percentage
progress bar while still keeping the output:

```bash
importer | pv --size=1453 > import.log
```
