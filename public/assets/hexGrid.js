
var hexGrid = {

    // The origin of the canvas
    origin: {
        x: 0,
        y: 0
    },

    context: null,
    canvas: null,

    options: {

        // Whether to make sectors randomly change, and on what time period
        randomise: {
            enable: true,
            interval: 100 //ms
        },

        // Whether some of the sectors should appear as 'corrupt'
        corruption: {
            enabled: true,
            percentage: 2 //%
        },

        // The set of chars that can appear in the sectors
        chars:  {
            'good': '1234567890ABCDEF',
            'corrupt': '!£$%^&*@~#?/\\¬<>+='
        },

        sector: {
            // Dimensions of each sector
            width: 18, //px
            height: 25, //px

            // Possible colours of each sector, randomly chosen
            colours: {
                ok: [
                    '#dddfde',
                    '#e7e9e6',
                    '#f2f2f2',
                    '#eaeaea',
                    '#ebebeb',
                    '#e8e8e8',
                    '#e5e5e5',
                    '#e0e0e0',
                    '#ededed',
                    '#e1e1df',
                ],
                corrupt: [
                    '#edced1',
                ],
            },
        },
    },

    // Set everything up
    init: function(canvasObject) {
        this.randomNumberGenerator = mulberry32((new Date()).getDate());

        // Add the canvas object to the hexGrid object
        this.canvas = canvasObject;

        // Get a drawing context
        this.context = this.canvas.getContext('2d');

        // Size the grid so it fills the document
        this.sizeToDocument();

        // Set the text alignment
        this.context.textAlign = 'center';
        this.context.font = '11px Ubuntu';

        // Render the sectors onto the canvas
        this.drawSectors();

        // Change sectors randomly
        if (this.options.randomise.enable) {
            window.setInterval(function () {
                var position = this.getRandomSectorLocation();

                this.drawSector(position.x, position.y);
            }.bind(this), this.options.randomise.interval);
        }
    },

    sizeToDocument: function() {
        // Get the size of the window
        this.canvas.width = document.documentElement.clientWidth;
        this.canvas.height = document.documentElement.clientHeight;

    },

	randomOkColour: function () {
        return this.options.sector.colours.ok[this.getRandomIntInclusive(0, (this.options.sector.colours.ok.length - 1))];
    },

	randomCorruptColour: function () {
        return this.options.sector.colours.corrupt[this.getRandomIntInclusive(0, (this.options.sector.colours.corrupt.length - 1))];
    },

	randomCharacter: function (charset) {

        // Set the default charset if its not been defined or cant be found
        if (typeof charset === 'undefined' || typeof this.options.chars[charset] === 'undefined') {
            charset = 'good';
        }

        return this.options.chars[charset].charAt(this.getRandomIntInclusive(0, (this.options.chars[charset].length - 1)));
    },

    drawSectors: function() {

        // Set initial coordinates to the origin of the canvas
        var coordX = this.origin.x, coordY = this.origin.y;

        // Loop until all available vertical space is taken
        while(coordY < this.canvas.height) {

            // Loop until all available horizontal space is taken
            while (coordX < this.canvas.width) {

                // Draw the individual sector
                this.drawSector(coordX, coordY);

                // Calculate the position for the next node
                coordX += this.options.sector.width;
            }
            // Set the X coordinate back to zero
            coordX = this.origin.x;

            // Jump one row down
            coordY += this.options.sector.height
        }
    },

    drawSector: function(coordX, coordY) {
        var corrupt = (this.options.corruption.enabled === true && this.getRandomIntInclusive(0, 100) <= this.options.corruption.percentage);

        var corruptSector = (typeof corrupt === 'boolean' && corrupt === true);

        // Determine the colours for this sector
        var colourSector = (corruptSector) ? this.randomCorruptColour():this.randomOkColour();
        var colourText = (corruptSector) ? '#FFFFFF':'#D0D0D0';
        var textCharset = (corruptSector) ? 'corrupt':'good';

        this.context.fillStyle = colourSector;
        this.context.fillRect(coordX, coordY, this.options.sector.width, this.options.sector.height);

        // Calculate the text positions
        var textCoordX = (coordX + (this.options.sector.width / 2));
        var textCoordY = (coordY + (this.options.sector.height / 2) + 4);

        // Draw the text
        this.context.font = '12px monospace';
        this.context.fillStyle = colourText;
        this.context.fillText(this.randomCharacter(textCharset), textCoordX, textCoordY);
    },

    getRandomSectorLocation: function() {
        return {
            x: this.options.sector.width * (Math.floor(Math.random() * (this.canvas.width / this.options.sector.width)) - 1),
            y: this.options.sector.height * (Math.floor(Math.random() * (this.canvas.height / this.options.sector.height)) - 1)
        };
    },

    getRandomIntInclusive: function(min, max) {
        return Math.floor(this.randomNumberGenerator() * (max - min + 1)) + min;
    }

};

function mulberry32(a) {
    return function() {
        let t = a += 0x6D2B79F5;
        t = Math.imul(t ^ t >>> 15, t | 1);
        t ^= t + Math.imul(t ^ t >>> 7, t | 61);
        return ((t ^ t >>> 14) >>> 0) / 4294967296;
    }
}

window.addEventListener('resize', () => {
    clearTimeout(window.timeout);
    window.timeout = setTimeout(
        () => {
            window.hexGrid.init(window.hexGrid.canvas);
        },
        100,
    );
});