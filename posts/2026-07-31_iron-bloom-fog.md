# Iron Bloom - The Fog

#project
#devlog



The game felt lifeless and flat. There was no motion at all other than the player jumping from one block to the next. I
thought, rather than having a flat black background I could add some texture in the form of a dark ominous fog, in the
pixelated style of course.

I tried a couple of different images, tweaking the pattern, colours, brightness, etc. It was definitely better, but
using a single static image didn't really feel right when everything else was dynamically generated. My first thought
was to generate a tileable image on the server. Then I thought why not take it a step further and ditch tileable
images completely and make the whole background a dynamic fog visualisation 🤔

I must admit, I didn't really know where to start with this one, I thought probably somekind of visual noise generator
would be needed. I turned to Claude. 

The result was a seeded '[value noise](https://en.wikipedia.org/wiki/Value_noise)' generator with [smoothstep](https://en.wikipedia.org/wiki/Smoothstep)
softening. Say what you like about AI, but I never would've attempted anything like this without it, and now I've
learned some really cool things.

There are three layers, a chunky/blobby layer, which gives an overall structure, a middle layer which adds some detail,
then the fine layer which has lots of detail, and is quite busy. They are all overlayed to give a shockingly realistic
misty/cloudy visualisation.

```html
<!--[eval class="full-bleed" style="height: 460px;"]-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { width: 100%; height: 100%; overflow: hidden; background: transparent; }
        #controls {
            position: fixed; top: 12px; left: 12px; z-index: 1;
            font: 13px/1.4 monospace; color: #ddd;
            background: rgba(0, 0, 0, 0.45); padding: 10px 12px; border-radius: 6px;
        }
        #controls label { display: block; cursor: pointer; user-select: none; }
        #controls input { margin-right: 6px; vertical-align: middle; }
    </style>
</head>
<body>
<div id="controls">
    <label><input type="checkbox" data-layer="0" checked>Layer 1 - chunky</label>
    <label><input type="checkbox" data-layer="1" checked>Layer 2 - middle</label>
    <label><input type="checkbox" data-layer="2" checked>Layer 3 - fine</label>
</div>
<script nonce="CSP_NONCE">
    function convertStringHashToNumber(s) {
        let h = 0x811c9dc5;
        for (let i = 0; i < s.length; i++) {
            h ^= s.charCodeAt(i);
            h = Math.imul(h, 0x01000193) >>> 0;
        }
        return h >>> 0 || 1;
    }

    function hash2d(x, y, s) {
        let h = Math.imul(s + (x | 0), 0x9e3779b9) >>> 0;
        h ^= h >>> 16;
        h = Math.imul(h + (y | 0), 0x85ebca6b) >>> 0;
        h ^= h >>> 15;
        h = Math.imul(h, 0xc2b2ae35) >>> 0;
        h ^= h >>> 16;
        return h / 0x100000000;
    }

    function smoothstep(t) {
        return t * t * (3 - 2 * t);
    }

    function valueNoise(x, y, s) {
        const ix = Math.floor(x), iy = Math.floor(y);
        const fx = smoothstep(x - ix), fy = smoothstep(y - iy);
        const a = hash2d(ix, iy, s);
        const b = hash2d(ix + 1, iy, s);
        const c = hash2d(ix, iy + 1, s);
        const d = hash2d(ix + 1, iy + 1, s);
        return a + (b - a) * fx + (c - a) * fy + (a - b - c + d) * fx * fy;
    }

    const layers = [
        { scale: 24, freq: 1.0, seedXor: 0x0000, weight: 0.55, enabled: true },
        { scale: 12, freq: 1.5, seedXor: 0xdead, weight: 0.30, enabled: true },
        { scale: 6,  freq: 2.5, seedXor: 0xbeef, weight: 0.15, enabled: true },
    ];

    function renderMist(ctx, seed, pixelSize = 64) {
        const { width, height } = ctx.canvas;

        if (!layers.some(layer => layer.enabled)) {
            return;
        }

        for (let x = 0; x <= Math.ceil(width / pixelSize); x++) {
            for (let y = 0; y <= Math.ceil(height / pixelSize); y++) {
                let n = 0;
                for (const layer of layers) {
                    if (!layer.enabled) continue;
                    n += valueNoise(
                            x / layer.scale * layer.freq,
                            y / layer.scale * layer.freq,
                            seed ^ layer.seedXor
                    ) * layer.weight;
                }

                const g = Math.round(17 + (n * 80));
                ctx.fillStyle = `rgb(${g},${g},${g})`;
                ctx.fillRect(x * pixelSize, y * pixelSize, pixelSize, pixelSize);
            }
        }
    }

    function createMistBackground(seed, pixelSize = 64) {
        const canvas = document.createElement('canvas');
        canvas.style.cssText = 'position:fixed;top:0;left:0;pointer-events:none;z-index:-1;';
        document.body.prepend(canvas);
        document.body.style.background = 'transparent';

        const ctx = canvas.getContext('2d');
        ctx.imageSmoothingEnabled = false;

        const render = () => {
            ctx.fillStyle = '#111';
            ctx.fillRect(0, 0, window.innerWidth, window.innerHeight);
            renderMist(ctx, seed, pixelSize);
        };

        const resize = () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            render();
        };

        window.addEventListener('resize', resize);
        resize();

        return render;
    }

    const render = createMistBackground(convertStringHashToNumber(new Date().toISOString().slice(0, 10)), 16);

    document.querySelectorAll('#controls input[data-layer]').forEach((input) => {
        input.addEventListener('change', () => {
            layers[Number(input.dataset.layer)].enabled = input.checked;
            render();
        });
    });
</script>
</body>
</html>
```


Try not to get [stuck in the fog, down a hole, with a' owl!](https://youtu.be/q_a1wxqloEs?si=Qa-bo3J-X5IyghPu&t=72)

