# 📻 Rendering Audio Waveforms

#PHP
#audio


For an upcoming post I wanted to include some audio, easy enough, just drop in an `{html}<audio>` tag, job done. Nah,
this sounds like a great oppurtunity for some overengineering!

I wanted to an audio visualiser, [no not that kind](https://www.youtube.com/watch?v=9TbLJI7ja4s), just a simple waveform
visualisation which follows along with the audio. I wanted it to be an enhancement to the native player rather than take
over and end up being a rebuild of the perfectly good native player.



## Attempt One: JS + OfflineAudioContext

My first attempt leveraged JS APIs provided by the browser to analyse the wav file, render the waveform to a canvas and
keep track of the current playback position:

```html
<!--[eval class="full-bleed" style="height: 120px;"]-->
<!DOCTYPE html>
<html>
<head>
    <style nonce="CSP_NONCE">
        body {
            background: #1a1a1a;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .player-shell {
            position: relative;
            width: 100%;
            max-width: 700px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,0.6);
        }

        #waveform {
            display: block;
            width: 100%;
            height: 120px;
            background: #111;
        }

        /* Floats over the canvas — does not affect layout height */
        .controls-wrap {
            position: absolute;
            left: 0;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            padding: 0 12px;
            transition: opacity 0.3s ease;
            /* Start visible so paused state always shows controls */
        }

        .controls-wrap.hidden {
            opacity: 0;
            pointer-events: none;
        }

        #player {
            display: block;
            width: 100%;
            background: transparent;
        }
    </style>
</head>
<body>

<div class="player-shell" id="shell">
    <canvas id="waveform"></canvas>
    <div class="controls-wrap" id="controlsWrap">
        <audio id="player" controls src="/sounds/hail.mp3"></audio>
    </div>
</div>

<script nonce="CSP_NONCE">
    const AUDIO_BOOST = 8;

    const audio        = document.getElementById('player');
    const canvas       = document.getElementById('waveform');
    const shell        = document.getElementById('shell');
    const controlsWrap = document.getElementById('controlsWrap');
    const ctx          = canvas.getContext('2d');

    let animFrame;
    let rawPeaks    = null;
    let audioCtx    = null;
    let gainNode    = null;
    let initialized = false;
    let hideTimer   = null;
    let isHovering  = false;

    // ── Controls visibility ──────────────────────────────────────────────────

    function showControls() {
        clearTimeout(hideTimer);
        controlsWrap.classList.remove('hidden');
    }

    function scheduleHide(delay = 2000) {
        clearTimeout(hideTimer);
        if (audio.paused) return; // always visible when paused
        hideTimer = setTimeout(() => {
            if (!isHovering) controlsWrap.classList.add('hidden');
        }, delay);
    }

    shell.addEventListener('mouseenter', () => {
        isHovering = true;
        showControls();
    });

    shell.addEventListener('mouseleave', () => {
        isHovering = false;
        scheduleHide(1000);
    });

    shell.addEventListener('touchstart', () => {
        showControls();
        scheduleHide(3000);
    }, { passive: true });

    audio.addEventListener('pause', showControls);
    audio.addEventListener('ended', showControls);
    audio.addEventListener('play',  () => scheduleHide(2000));

    // ── Canvas sizing ────────────────────────────────────────────────────────

    function resizeCanvas() {
        const rect = canvas.getBoundingClientRect();
        const dpr  = window.devicePixelRatio || 1;
        canvas.width  = rect.width  * dpr;
        canvas.height = rect.height * dpr;
        ctx.scale(dpr, dpr);
        if (rawPeaks) drawWaveform(audio.duration ? audio.currentTime / audio.duration : 0);
    }

    // ── Build normalised peaks ───────────────────────────────────────────────

    async function buildPeaks(audioBuffer) {
        const data    = audioBuffer.getChannelData(0);
        const rect    = canvas.getBoundingClientRect();
        const samples = Math.floor(rect.width);
        const block   = Math.floor(data.length / samples);
        const peaks   = new Float32Array(samples);
        let globalMax = 0;

        for (let i = 0; i < samples; i++) {
            let max = 0;
            for (let j = 0; j < block; j++) {
                const abs = Math.abs(data[i * block + j]);
                if (abs > max) max = abs;
            }
            peaks[i] = max;
            if (max > globalMax) globalMax = max;
        }

        if (globalMax > 0) {
            for (let i = 0; i < samples; i++) peaks[i] /= globalMax;
        }

        return peaks;
    }

    // ── Draw ─────────────────────────────────────────────────────────────────

    function drawWaveform(playFraction = 0) {
        const rect  = canvas.getBoundingClientRect();
        const W     = rect.width;
        const H     = rect.height;
        const mid   = H / 2;
        const playX = Math.floor(playFraction * W);

        ctx.fillStyle = '#111';
        ctx.fillRect(0, 0, W, H);

        for (let i = 0; i < rawPeaks.length; i++) {
            const amp = rawPeaks[i] * mid * 0.9;
            ctx.fillStyle   = '#1db954';
            ctx.globalAlpha = i < playX ? 1.0 : 0.35;
            ctx.fillRect(i, mid - amp, 1, amp * 2);
        }

        ctx.globalAlpha = 1.0;

        if (playFraction > 0 && playFraction < 1) {
            ctx.strokeStyle = '#ffffff';
            ctx.lineWidth   = 1.5;
            ctx.shadowColor = '#ffffff';
            ctx.shadowBlur  = 6;
            ctx.beginPath();
            ctx.moveTo(playX, 0);
            ctx.lineTo(playX, H);
            ctx.stroke();
            ctx.shadowBlur  = 0;
        }
    }

    // ── Decode for waveform ──────────────────────────────────────────────────

    async function init() {
        const response    = await fetch(audio.src);
        const arrayBuffer = await response.arrayBuffer();
        const offline     = new OfflineAudioContext(1, 1, 44100);
        const decoded     = await offline.decodeAudioData(arrayBuffer);

        rawPeaks = await buildPeaks(decoded);
        drawWaveform(0);
    }

    // ── Audio graph ──────────────────────────────────────────────────────────

    function initAudioGraph() {
        if (initialized) return;
        initialized = true;

        audioCtx            = new AudioContext();
        const source        = audioCtx.createMediaElementSource(audio);
        gainNode            = audioCtx.createGain();
        gainNode.gain.value = AUDIO_BOOST;

        source.connect(gainNode);
        gainNode.connect(audioCtx.destination);
    }

    // ── Animate playhead ─────────────────────────────────────────────────────

    function animate() {
        if (!rawPeaks) return;
        drawWaveform(audio.duration ? audio.currentTime / audio.duration : 0);
        animFrame = requestAnimationFrame(animate);
    }

    audio.addEventListener('play', () => {
        initAudioGraph();
        if (audioCtx.state === 'suspended') audioCtx.resume();
        cancelAnimationFrame(animFrame);
        animate();
    });

    audio.addEventListener('pause', () => cancelAnimationFrame(animFrame));
    audio.addEventListener('ended', () => {
        cancelAnimationFrame(animFrame);
        drawWaveform(0);
    });

    audio.addEventListener('seeked', () => {
        if (audio.paused && rawPeaks) drawWaveform(audio.currentTime / audio.duration);
    });

    window.addEventListener('resize', resizeCanvas);

    resizeCanvas();
    init();
</script>
</body>
</html>
```

It worked, but I wasn't happy with it. It was visually a bit much, and it felt rude to ask each visitor to do a bunch
of audio analysis just to render a totally unnecessary image, especially given that the image won't ever change. Also
I didn't want to deal with browser compatibility.



## Attempt Two: PHP + ffmpeg + GD

This moves all the work to the server and leverages the excellent ffmpeg to do the audio processing:

```
ffmpeg -i sound.mp3
    -ac 1                    // mono (1 channel)
    -filter:a aresample=8000 // resample to 8 kHz — reduces data volume
    -map 0:a                 // select audio stream from input 0
    -c:a pcm_s16le           // encode as 16-bit signed little-endian Pulse Code Modulation
    -f data                  // raw output format (no container)
```

This converts the audio file into raw amplitude values, these values are output in a binary format and can be converted
into an array of integers in PHP by calling `{php}unpack('v*', $binaryOutput)`. It's a small jump then to plot these
values to a simple PNG:

<img src="/audio-waveform-image?fileName=hail.mp3" class="full-bleed" loading="lazy" width="700" height="60" />

The native audio player is then layered on top (it hides when de-focused) with a little CSS and the whole thing is
packaged up into an `{html}<x-audio/>` webcomponent.

<x-audio src="hail.mp3" gain="8"></x-audio>

The soruce code for the [webcomponent](https://git.mountainofcode.co.uk/mountainofcode.co.uk/file/public/assets/📻.js)
and [image generation](https://git.mountainofcode.co.uk/mountainofcode.co.uk/file/views/audio-waveform-image.php) are,
naturally, opensource.
