I use command line arguments in nearly every bash script I write, they are super useful but when it comes to parsing the arguments it's always a pain.

So I wrote a library that makes it easy!

<!-- more -->

## The Current Options

Bash has a function for parsing arguments, the `getopts` function, but it's just not very good. It doesn't support long form arguments like `--file-name` or `--match="[0-9]"` and has this really weird way of defining the arguments to accept.

There is also `getopt`, while this isn't a bash function it seems to be commonly available, but this is full of problems to, partial support for the various argument types and there are plenty of people who say to avoid it.

It seems like every blog post, Stack Overflow article and comment I read where someone is trying to parse arguments all end up with the same answer, roll your own. So I did!

## REGEX To The Rescue

What better way to match known patterns in a string than REGEX, and as I stumbled across a little while ago, bash has native support for REGEX.

### First Attempt...

My first attempt went some like this:

```bash
args=" $@ "

regexArgComment=' -(-comment|c) ([^ ]+) '
[[ $args =~ $regexArgComment ]]
if [ "${BASH_REMATCH[2]}" != "" ]; then
	# Do something
fi

```

This was better as I could specify long and short arguments and even pattern match the value, eg only numbers.

It still had a bunch of problems though, I had to write a new regex pattern out for every argument, it couldn't do short form parameter chaining (`-aih` instead of `-a -i -h`) but the worst of all was that I couldn't specify argument values with whitespace in no matter how much I played with the regex.

### A Better Solution

It wasn't long before I was throughly bored of the limitations of my first solution and wanted something better.

All it took was the realisation that I had all the information I wanted available to me through the variables I used initialy, namely the `$1`, `$2`, `$3` etc variables. Each one contained a space separated 'chunk' of the command used to run the script.

So now all I needed to do was work out if each of those chunks was an argument, REGEX can easily do that, and use the assumption that if one of the chunks isn't an argument then it is the value of the argument in the previous chunk!

I also ended up running a filter before looping over the chunks that expanded out chained short form arguments, ie `-prT` was turned into `-p -r -T`

## Accessing The Arguments

Now that I could logically determine an argument and its value all I needed was a way to easily store and access the data and for this I just used an associative array, this does limit support to Bash v4 but as that was released in 2009 i'm not going to worry.

I wrote a couple of really simple functions to access the array a little more easily. Both take only one argument, the name of the argument to look up.

- `argValue()` - Returns the value of the argument if it has been defined.
- `argExists()` - Returns a boolean depending on whether the argument has been defined or not. 

## Conclusion

If you want to use mixed format arguments, want to be able to group short arguments together or use arguments that contains white space, take a look at my library on [GitHub](https://github.com/moebrowne/bash-argument-parser).

