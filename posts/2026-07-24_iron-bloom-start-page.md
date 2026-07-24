# Iron Bloom - Start Page

#project
#devlog

Lots more progress has been made on the game. One notable improvement is the start page. Until now, starting a new game
required manually clearing cookies to get a new session and therefore a new seed.

I also wanted the player to be able to enter a seed. I also thought making the seed obvious would be useful for testing
and debugging later.

It started simple:

![](/images/iron-bloom-start-page-0.png)



## Seed Generator

In an attempt to make the whole game feel as dynamic and unique as possible, I added a seed randomiser. It was also an
excellent opportunity for some humour 😆

The generator is very simple. It randomly picks an adjective and a noun from hardcoded lists and bungs them together.
This gives seeds like "wheezing-drivel", "lumpy-blotch", "decrepit-buttocks", "frantic-marmalade", "bloated-codpiece",
and on and on.

There are currently 1,734 possible combinations. I strongly suspect we will add more words, I also might expand it to
three words.

Initially, the fields' default value was populated with a random seed. To get a new one, you had to refresh the page,
this was good enough for testing, but I wanted to do something better.

I added a button to the right of the field which would fetch a new seed and drop it in the field. I wanted to use an
icon for the button label and initially had the classic refresh arrow-in-a-circle. This stuck out like a sore thumb,
curves have no place in the pixel-art style. I tried creating a square version, but it was janky at best. I had a
better idea.

Dice! Specifically, ASCII dice: ⚀⚁⚂⚃⚄⚅

When the button is clicked the icon randomly shuffles through the different dice faces, as though it was being rolled.
Give it a go:

```html
<!--[eval class="full-bleed" style="height: 160px;"]-->
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>Seed Input</title>
    <style>
      html, body {
        margin: 0;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: monospace;
        font-size: 16px;
        color: #ccc;
      }

      .input-wrapper {
        min-width: 280px;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        padding: 8px;
        background: #1a1a1a;
        border: 1px solid #444;
        color: #eee;
        font-family: inherit;
      }

      .input-wrapper label {
        pointer-events: none;
        font-size: 0.85rem;
        letter-spacing: 0.1em;
        color: #9f9f9f;
        text-transform: uppercase;
      }

      .input-wrapper input {
        flex: 1 0 auto;
        background-color: transparent;
        border: 1px solid transparent;
        color: inherit;
        font-size: 1rem;
        font-family: inherit;
        outline: none;
        text-transform: uppercase;
      }

      .input-wrapper input:focus {
        border-color: #777;
      }

      .input-wrapper input::selection {
        background: #444;
        color: #eee;
      }

      .input-wrapper button {
        margin-top: -8px;
        padding: 0;
        background: none;
        border: none;
        cursor: pointer;
        color: #555;
        font-size: 2.7rem;
        line-height: 0.7;
      }
    </style>
  </head>
  <body>
    <div class="input-wrapper">
      <label for="seed">Seed:</label>
      <input
        type="text"
        id="seed"
        name="seed"
        value=""
        autocomplete="off"
      />
      <button type="button" id="refresh-seed" title="Randomise">⚂</button>
    </div>

    <script nonce="CSP_NONCE">
      const adjectives = [
        'absurd','ample','baffled','bleary','bloated','blundering','brooding','bulging',
        'clammy','crestfallen','cursed','damp','decrepit','dishevelled','dripping','dubious',
        'exquisite','feeble','festive','flacid','flustered','frantic','frumpy','fumbling',
        'furious','ghastly','giddy','grim','gnarled','haggard','incredible','lanky','leaking',
        'limp','lopsided','lumpy','moist','murky','plump','pompous','ponderous','pungent',
        'quivering','rancid','rotten','rotund','shambolic','smug','soggy','sloppy','spindly',
        'stinky','sullen','sulky','tepid','thunderous','turgid','vast','vexed','vile','wet',
        'wheezing','wobbly','wretched',
      ];

      const nouns = [
        'badger','blotch','blunder','bumpkin','bunion','buttocks','bulge','cabbage','codpiece',
        'crevice','custard','drivel','drool','fungus','giblet','goblin','gruel','haggis',
        'lobster','lurker','marmalade','mucus','noodle','pigeon','porridge','pothole','puddle',
        'rascal','runoff','sausage','sandwich','scallywag','seed','snivel','squelch','tripe',
        'turnip','waffle',
      ];

      function randomSeed() {
        const adj = adjectives[Math.floor(Math.random() * adjectives.length)];
        const noun = nouns[Math.floor(Math.random() * nouns.length)];
        return `${adj}-${noun}`;
      }

      const dice = ['⚀', '⚁', '⚂', '⚃', '⚄', '⚅'];

      async function refreshSeed() {
        const btn = document.getElementById('refresh-seed');
        const interval = setInterval(
          () => (btn.textContent = dice[Math.floor(Math.random() * dice.length)]),
          100,
        );
        
        await new Promise(resolve => setTimeout(resolve, 600));
        clearInterval(interval);
        
        document.getElementById('seed').value = randomSeed();
      }

      document.getElementById('refresh-seed').addEventListener('click', refreshSeed);

      refreshSeed();
    </script>
  </body>
</html>
```

I also created an image for the title which uses a pixel style font where each pixel is on of the floor tiles from the
game.

![](/images/iron-bloom-start-page.png)
