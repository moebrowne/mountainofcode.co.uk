# Bash History

#bash
#CLI

I make heavy use of <kbd>Ctrl</kbd> + <kbd>R</kbd> in my terminal, it allows me to quickly search for previously run
commands. By default, the history of a Bash terminal is only written to disk, and therefore searchable in other
terminals when the terminal closes. It also overwrites the whole history file. This is annoying as I usually have many
terminals open, and they all get closed at different times.

This can be fixed with two additions to the `.bashrc` file:

```bash
shopt -s histappend
PROMPT_COMMAND="history -a;$PROMPT_COMMAND"
```

The first of these lines instructs Bash to append to the history file rather than overwriting it. The second causes the
history to be updated each time a command completes.

At first, I thought that only the second line would be required because that seems to do everything that's required,
each time a command is run it's written to the history file. The problem is that when the terminal exits, it will try to
overwrite the history file with any unwritten history but there is none, so the history file is overwritten with nothing
AKA it's truncated. 


## Infinite History

On Ubuntu the default history length is 2000 lines. I think there is very little reason not to keep everything. I also
opt to ignore repeated commands and any which start with a space:

```bash
HISTFILESIZE=
HISTSIZE=
HISTCONTROL=ignoreboth
```

