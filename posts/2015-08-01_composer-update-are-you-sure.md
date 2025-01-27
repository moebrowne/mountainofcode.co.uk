# 🧑‍💻 Composer Update On Production Servers

#automation
#composer
#cron

When you dont have an automated deployment system setup and you push code to the production server you will need to 
manually run all the task runners, namely Composer.

All the articles you read will tell you to never run `composer update` on a production server but why? and why does it 
not warn me?

## `composer update` On Production Server

There are already a million and one articles about why you shouldn't run `composer update` on a production server the 
but short version is `composer update` will download the very latest code which could be incompatible or broken.

## Can't You Just Remember To `composer install`?

The problem is that when you're developing you'll run `composer update` a million times and `composer install` hardly 
ever, to the extent where it's nearly muscle memory and it's only a matter of time before new untested code ends up 
breaking something on your production server.

## Adding An 'Are You Sure'

Using a simple bash function and an alias you can easily intercept calls to `composer update` and prompt you for a 
confirmation.

In `~/.bashrc` on the server add the following:

```bash
function composer_sure() {
            
        if [[ "$1" != "update" ]]; then
                composer $@
            
        else    
                echo -e "\e[41mWARNING Composer update shouldn't be run on a production server\e[49m"
                echo -n "Are you sure you want to do this? [y/N] "
                read -r -p "" response;
                case $response in
                        [yY][eE][sS]|[yY])
                                composer $@;;
                esac
        fi
}
alias composer="composer_sure"
```

This will give you reminder but not prevent you from running `composer update` should you want to.
