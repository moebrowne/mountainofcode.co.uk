# Git Branch Download

#bash
#git
#shell

Some times you want to clone a repository but you dont want all the history and git stuff to. This is a pretty simple task:

```bash
git clone user@repohost:repo-name
git checkout the-branch-i-want
rm -r .git
```

But it can be easier!

## Git Download

I put together a simple bash script that simplifies this process to a single command, allows you to pick the branch you want to download and does a couple of checks to prevent you doing anything destructive.

You can get a copy of the script [here](https://github.com/moebrowne/git-download).

If you wanted to make this even easier and more git-like you could alias this script to `git download`
