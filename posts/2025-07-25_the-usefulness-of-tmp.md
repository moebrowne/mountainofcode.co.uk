# Use The /tmp Luke

#UNIX
#tmp
#workflow

Over the last couple of years I have come to use the `/tmp` directory on a daily basis.

It came about because I was frustrated at all the old downloads, screenshots, database dumps, test files, etc. that
would clutter up my home folder. Every now and again, I would manually go through and delete stuff, but I wanted a way
of automatically clearing it out. I thought about writing some kind of script which would select folders which hadn't
been read/written to in x days.

All the ideas I had were kinda gross and felt like I was going to end up inadvertently deleting something important.

It occurred to me some time later that the solution to my problem had already been invented, many, many years ago:
`/tmp`.  Some people will point out how using `/tmp` is unsecure because it is globally accessible, while this is true
I'm the only user on the machine, and I'm not storing sensitive stuff there. I'm not going to worry.

I created an alias which will quickly create a new temporary directory and drop me into it:
`{bash}alias cdmktemp="cd $(mktemp -d)"`. I can mess around with stuff in there, and then cd away knowing it will all
get cleaned up on restart.
