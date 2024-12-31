# Download Single File From Private BitBucket Repo

#automation
#bitbucket
#git
#ssh

Today I was writing a script that needs to run without user interaction and need to get the latest version of a single
file from a private BitBucket Git repo over SSH.

BitBucket allows you to do this over HTTPS and I could use something like `curl` or `wget` with digest auth but then
that would require the user name and password to be added to the script in plain text...

Not ideal, especially when SSH keys are already setup and far more secure than passwords, but there is a solution...

## Git Archive

After a little more research I stumbled across `git archive`. From the man page: 'Create an archive of files from a
named tree'... Perfect!

This allows me to do exactly what if I do something like this:

```bash
git archive --remote=git@bitbucket.org:<USERNAME>/<REPONAME>.git HEAD <FILENAME> | tar -xf - --to-stdout <FILENAME>
```

You notice the Git output is piped through `tar` this is because the Git archive outputs a tar archive.
I assume this works with GitHub but I haven't tested it.
