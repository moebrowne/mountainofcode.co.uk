# Composer: global config vs config --global

#Composer
#TIL

Do you know the difference between `composer global config` and `composer config --global`? Me neither until a couple of
days ago!

The difference is subtle.

`composer global config` updates `~/.config/composer/composer.json`. This is config for the 'global project', the place
where global dependencies are installed.

`composer config --global` updates `~/.config/composer/config.json`. This is config for Composer itself. For example,
authentication keys.

