class AudioVisualiser extends HTMLElement {
    static observedAttributes = ['src', 'gain'];

    constructor() {
        super();
        this.attachShadow({mode: 'open'});
        this.shadowRoot.innerHTML = `
            <style>
                :host {
                    display: block;
                    position: relative;
                    width: 100%;
                    overflow: hidden;
                    background: #111;
                }

                .waveform {
                    position: absolute;
                    inset: 0;
                    display: block;
                    width: 100%;
                    top: 4px;
                    height: calc(100% - 8px);
                    object-fit: fill;
                }

                /* Desaturate everything to the right of --progress so the
                   unplayed portion of the bars reads as grey */
                :host::after {
                    content: '';
                    position: absolute;
                    inset: 0 0 0 var(--progress, 0%);
                    backdrop-filter: grayscale(1);
                    pointer-events: none;
                }

                audio {
                    min-height: 40px;
                    display: block;
                    position: relative;
                    z-index: 1;
                    width: 100%;
                    background: transparent;
                    transition: opacity 0.3s ease;
                }

                audio.hidden {
                    opacity: 0;
                    pointer-events: none;
                }
            </style>
            <img class="waveform" alt="">
            <audio controls loading="lazy"></audio>
        `;

        this.waveformImg = this.shadowRoot.querySelector('img.waveform');
        this.audio = this.shadowRoot.querySelector('audio');

        this.animFrame = null;
        this.audioCtx = null;
        this.gainNode = null;
        this.hideTimer = null;
        this.isHovering = false;
    }

    connectedCallback() {
        this.applySrc();
        this.applyGain();
        this.bindAudioEvents();
        this.bindHoverEvents();
    }

    attributeChangedCallback(name, _oldVal, _newVal) {
        if (!this.isConnected) {
            return;
        }

        if (name === 'src') {
            this.applySrc();
        }

        if (name === 'gain') {
            this.applyGain();
        }
    }

    applySrc() {
        const src = this.getAttribute('src');

        if (!src) {
            throw new Error('No src attribute provided');
        }

        this.audio.src = `/sounds/${src}`;
        this.waveformImg.src = `/audio-waveform-image?fileName=${encodeURIComponent(src)}`;
    }

    applyGain() {
        if (this.gainNode) {
            this.gainNode.gain.value = parseFloat(this.getAttribute('gain')) || 1;
        }
    }

    initAudioGraph() {
        if (this.audioCtx) {
            return;
        }

        this.audioCtx = new AudioContext();

        const source = this.audioCtx.createMediaElementSource(this.audio);

        this.gainNode = this.audioCtx.createGain();
        this.gainNode.gain.value = parseFloat(this.getAttribute('gain')) || 1;

        source.connect(this.gainNode);

        this.gainNode.connect(this.audioCtx.destination);
    }

    setProgress(fraction) {
        this.style.setProperty('--progress', Math.round(fraction * 1000) / 10 + '%');
    }

    bindAudioEvents() {
        const tick = () => {
            if (this.audio.duration) {
                this.setProgress(this.audio.currentTime / this.audio.duration);
            }

            this.animFrame = requestAnimationFrame(tick);
        };

        this.audio.addEventListener('play', () => {
            this.initAudioGraph();

            if (this.audioCtx.state === 'suspended') {
                this.audioCtx.resume();
            }

            cancelAnimationFrame(this.animFrame);
            tick();
            this.scheduleHide(2000);
        });

        this.audio.addEventListener('pause', () => {
            cancelAnimationFrame(this.animFrame);
            this.showControls();
        });

        this.audio.addEventListener('ended', () => {
            cancelAnimationFrame(this.animFrame);
            this.setProgress(0);
            this.showControls();
        });

        this.audio.addEventListener('seeked', () => {
            if (this.audio.duration) {
                this.setProgress(this.audio.currentTime / this.audio.duration);
            }
        });
    }

    showControls() {
        clearTimeout(this.hideTimer);
        this.audio.classList.remove('hidden');
    }

    scheduleHide(delay = 2000) {
        clearTimeout(this.hideTimer);

        if (this.audio.paused) {
            return;
        }

        this.hideTimer = setTimeout(
            () => {
                if (!this.isHovering) {
                    this.audio.classList.add('hidden');
                }
            },
            delay
        );
    }

    bindHoverEvents() {
        this.addEventListener('mouseenter', () => {
            this.isHovering = true;
            this.showControls();
        });

        this.addEventListener('mouseleave', () => {
            this.isHovering = false;
            this.scheduleHide(1000);
        });

        this.addEventListener('touchstart', () => {
            this.showControls();
            this.scheduleHide(3000);
        }, {passive: true});
    }
}

customElements.define('x-audio', AudioVisualiser);
