# Columnated Coloursied Content In The Shell

#bash
#column
#shell

When I was building my [Multi SSH Key Manager](http://mountainofcode.co.uk/2015/04/24/Multi-SSH-Key-Manager/) I wanted to display a nice neat table with all the colour coded data in, how hard can that be.. Apparently it's not easy...

## The `column` Command Is Great

Put simply the `column` command will take a string or a file and a delimiter and print out a nicely spaced table to the screen, great! So I can do:

```bash
#!/bin/bash

row0="alpha bravo charlie";
row1="delta echo foxtrot";
row2="golf hotel india";

echo "$row0
$row1
$row2" | column -t -s' '
```

You get the following output:

```html
alpha  bravo  charlie
delta  echo   foxtrot
golf   hotel  india
```

Cool, it doesn't even care if the content in the columns varies, it allows for it.

## A Splash Of Colour

So here's where `column` falls down...

If we add in some colour codes to the output and turn on interpretation of backslash escapes using `echo -e`, like so:

```sh
#!/bin/bash

row0="\e[0;31malpha\e[0m bravo charlie";
row1="delta echo foxtrot";
row2="golf hotel india";

echo -e "$row0
$row1
$row2" | column -t -s' '
```

You get this output:

```html
alpha  bravo  charlie
delta           echo   foxtrot
golf            hotel  india
```

*Note: alpha will show up in red if you actually ran it in a shell*

Well this is weird and not what we expected.. So what's going on, well all becomes clear if we turn off interpretation of backslash escapes, `echo` rather than `echo -e` and run it again:

```html
\e[0;31malpha\e[0m  bravo  charlie
delta               echo   foxtrot
golf                hotel  india
```

I guess technically it's doing what it should, it's taking the escape sequences into account when calculating the column widths and why shouldn't it, `column` has no idea what the terminal will do with the escape characters...

So I could leave out the colours and it would work just fine, but where's the fun in that!

## I Want My Colourful Table Damn It!

Not one to be defeated by a challenge I spent a good long time researching this problem and came up with a solution, it's not as easy to use as `column` but it works and looks good doing it, and as we all know bash is all about aesthetics... :P

The way to do it to use so called cursor movement escape sequences, they allow you to set the position of the cursor when printing to the screen.

### How Do They Work?

The syntax is like this: `\033[XXG` where `XX` is the number of characters from the start of the line you want to continue printing from, for example:

```bash
#!/bin/bash

row0="alpha \033[10G bravo \033[20G charlie";
row1="delta \033[10G echo \033[20G foxtrot";
row2="golf \033[10G hotel \033[20G india";
echo -e "$row0
$row1
$row2"
```

*I added in the spaces around the `\033[XXG` escape sequences to make it more readable, they aren't necessary to make it work.*

This gives something that looks like this:

```html
alpha     bravo     charlie
delta     echo      foxtrot
golf      hotel     india
```

Cool, we've got a table with columns that start at character 10 and 20 but even better, we can add as many colours and escape codes as we want and it will still format correctly!

## It's Not Perfect.. At All

There are a number of caveats with this approach:

- You have to specify the column widths manually.
- The positions are relative to the start of the line not the column to the left.
- A cells content will be truncated by the next one to the right if its content is wider than the cell.

I plan in the future to make my own version of column that takes some of the pain out of this...
