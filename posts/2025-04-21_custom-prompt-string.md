# Custom $PROMPT_COMMAND

#bash
#CLI
#PS1

Did you know you can customise the `user@host:/path $` prompt which appears in your terminal? It's called the
<abbr title="Prompt String 1">PS1</abbr>, I've known it can be changed for ages but I'd never seen any examples which
added any real utility, they were always overblown and kinda silly, and I'd mentally marked it as not-useful and ignored
it ever since. I also spent quite a lot of my time switching between servers where the prompts had to stay as the
defaults.

Recently I was looking at the prompt thinking that there is quite a lot in there that never changes. For example on my
single user dev machine I don't need to see my username or hostname, they never change and just take up space.

A couple of hours of hacking later I'd settled on a prompt which was just `/path/to/cwd/ $`, this is considerably
shorter than the default `joeblogs@pc-1234-abcd:/path/to/cwd/ $`.

I also sprinkled in a little Git info when the current working directory is a repo, simply the current branch name
followed by a * or ➚ if there are uncommitted/unpushed changes.

![ps1.png](/images/ps1.png)

If you want something similar dump this in your `~/.bashrc` file:

```bash
# Git prompt components
GIT_PROMPT_PREFIX=" \[\e[1;35m\]"
GIT_PROMPT_SUFFIX="\[\e[0m\]"
GIT_PROMPT_DIRTY="*"
GIT_PROMPT_UNPUSHED="➚"
GIT_PROMPT_CLEAN=""

# Flag to track if this is the first prompt
FIRST_PROMPT=true

function parse_git_branch() {
    git branch 2> /dev/null | sed -e '/^[^*]/d' -e 's/* \(.*\)/\1/'
}

function parse_git_status() {
    local status=""

    # Check for dirty/untracked files
    local git_status="$(git status -s 2> /dev/null)"
    if [[ -n "$git_status" ]]; then
        status+="$GIT_PROMPT_DIRTY"
    fi

    # Check for unpushed commits
    local branch=$(parse_git_branch)
    if [[ -n "$branch" ]]; then
        local remote=$(git for-each-ref --format='%(upstream:short)' "refs/heads/$branch" 2> /dev/null)
        if [[ -n "$remote" ]]; then
            local unpushed=$(git rev-list "$remote..HEAD" 2> /dev/null | wc -l)
            if [[ $unpushed -gt 0 ]]; then
                status+="$GIT_PROMPT_UNPUSHED"
            fi
        else
            # If no upstream is set, mark as unpushed
            status+="$GIT_PROMPT_UNPUSHED"
        fi
    fi

    echo -n "$status"
}

function git_prompt_info() {
    local branch=$(parse_git_branch)
    if [[ -n "$branch" ]]; then
        echo -n "$GIT_PROMPT_PREFIX$branch$(parse_git_status)$GIT_PROMPT_SUFFIX"
    fi
}

# Set the prompt
function set_bash_prompt() {
    # Need to capture the output of the functions for PS1
    local git_info="$(git_prompt_info)"

    # Set the terminal title to the current working directory
    echo -ne "\033]0;${PWD}\007"

    # Conditional newline based on whether this is the first prompt
    local newline=""
    if [[ "$FIRST_PROMPT" = false ]]; then
        newline='\n'
    else
        FIRST_PROMPT=false
    fi

    # Prompt with conditional newline
    PS1="$newline"'\[\e[1;32m\]\w/\[\e[0m\]'"$git_info"' $ '
}

PROMPT_COMMAND=set_bash_prompt
```


## Update (27 Sept 2025) - Now With More Kubernetes

I found it useful as a sanity check to know which Kubernetes cluster my current CLI is targetting. It will only show
once a `kubectl` command is run so it isn't constantly cluttering up the CLI. This isn't ideal as you'd want to know
before you run the command but in the vast majority of cases I will run a read-only command first like `kubectl get nodes`


```bash
# Git prompt components
GIT_PROMPT_PREFIX=" \[\e[1;35m\]"
GIT_PROMPT_SUFFIX="\[\e[0m\]"
GIT_PROMPT_DIRTY="*"
GIT_PROMPT_UNPUSHED="➚"
GIT_PROMPT_CLEAN=""

# Kubernetes prompt components
K8S_PROMPT_PREFIX=" \[\e[1;36m\]["
K8S_PROMPT_SUFFIX="]\[\e[0m\]"

# Flag to track if this is the first prompt
FIRST_PROMPT=true

# Variable to track if kubectl has been used
KUBECTL_USED=false

function parse_git_branch() {
    git branch 2> /dev/null | sed -e '/^[^*]/d' -e 's/* \(.*\)/\1/'
}

function parse_git_status() {
    local status=""

    # Check for dirty/untracked files
    local git_status="$(git status -s 2> /dev/null)"
    if [[ -n "$git_status" ]]; then
        status+="$GIT_PROMPT_DIRTY"
    fi

    # Check for unpushed commits
    local branch=$(parse_git_branch)
    if [[ -n "$branch" ]]; then
        local remote=$(git for-each-ref --format='%(upstream:short)' "refs/heads/$branch" 2> /dev/null)
        if [[ -n "$remote" ]]; then
            local unpushed=$(git rev-list "$remote..HEAD" 2> /dev/null | wc -l)
            if [[ $unpushed -gt 0 ]]; then
                status+="$GIT_PROMPT_UNPUSHED"
            fi
        else
            # If no upstream is set, mark as unpushed
            status+="$GIT_PROMPT_UNPUSHED"
        fi
    fi

    echo -n "$status"
}

function git_prompt_info() {
    local branch=$(parse_git_branch)
    if [[ -n "$branch" ]]; then
        echo -n "$GIT_PROMPT_PREFIX$branch$(parse_git_status)$GIT_PROMPT_SUFFIX"
    fi
}

function get_k8s_context() {
    if [[ "$KUBECTL_USED" = true ]]; then
        local context=$(kubectl config current-context 2>/dev/null)
        if [[ -n "$context" ]]; then
            echo -n "$K8S_PROMPT_PREFIX$context$K8S_PROMPT_SUFFIX"
        fi
    fi
}

# Function to track kubectl usage
function kubectl() {
    KUBECTL_USED=true
    command kubectl "$@"
}

# Set the prompt
function set_bash_prompt() {
    # Need to capture the output of the functions for PS1
    local git_info="$(git_prompt_info)"
    local k8s_info="$(get_k8s_context)"

    # Set the terminal title to the current working directory
    echo -ne "\033]0;${PWD}\007"

    # Conditional newline based on whether this is the first prompt
    local newline=""
    if [[ "$FIRST_PROMPT" = false ]]; then
        newline='\n'
    else
        FIRST_PROMPT=false
    fi

    # Prompt with conditional newline
    PS1="$newline"'\[\e[1;32m\]\w/\[\e[0m\]'"$git_info$k8s_info"' $ '
}

PROMPT_COMMAND=set_bash_prompt
```
