# Project Iron Bloom

#project
#devlog

[A friend](https://itmecho.com/) and I are developing a game. I'm going to try and keep a dev-log of sorts, share
anything interesting and vaguely documenting the process.



## The Plan

We created a `DESIGN.md` document and pulled together some ideas. We decided on an old-school, pixel-art, top-down,
procedurally generated, turn-based, 8-bit, zelda-esk, exploration, dungeon crawler style thing. I'm sure it'll evolve
over time, but that's the general direction.

We're using PHP and a sprinkinling of JS. All interaction is via the keyboard. Each keystroke is sent to the server, and
it responds with an image. The image is the whole play area, inventory, map, etc. It's kind of like [dynamic image
streaming](/php-jpeg-stream) but each frame waits for a keystroke.



## Start Simple, Start Small

![](/images/iron-bloom-0.png)

This is the first iteration. It's just a randomly sized room, but you can move around (yes, that thing in the middle is
the player 😆) and can't go beyond the walls. It also persists all game state to `{php}$_SESSION`.

I want players to be able to enter a custom seed at the start of each game (I also think it'll make testing easier), so
to generate random numbers we use:

```php
mt_srand(crc32($seed)); // crc32 is a quick and dirty way to convert strings to integers.
$number = mt_rand(5, 30);
```


## Further Posts

- [Pixel Art](/iron-bloom-art)
