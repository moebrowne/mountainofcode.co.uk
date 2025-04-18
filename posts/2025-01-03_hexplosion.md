# Hexplosion Demo

#demo
#hexaons are the bestagons
#project

A demo of an old project. Source is here: [https://github.com/moebrowne/hexplosion](https://github.com/moebrowne/hexplosion)

Click anywhere below!

```html
<!--[eval class="full-bleed" style="height: 600px;"]-->
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grid Render</title>
    <style>
        html,
        body {
            margin: 0;
            height: 100%;
            background-color: #017DBD;
            overflow: hidden;
        }
    </style>
</head>
<body>

<canvas id="cont"></canvas>

<script>
    class Hexplosion
    {
        // Set everything up
        constructor(canvasObject) {

            this.hexagon = {
                radius: 8,
                rippleSize: 8
            };

            this.hexagons = [];

            // Add the canvas object to the hexGrid object
            this.canvas = canvasObject;

            // Get a drawing context
            this.context = this.canvas.getContext('2d');

            // Add event handlers
            this.canvas.addEventListener('click', e => {
                this.explode({
                    x: e.offsetX,
                    y: e.offsetY
                })
            });

            this.resizeCanvas();

            // Start drawing
            this.drawAll();
        }

        /**
         * Changes hexagon radius
         * @param radius
         */
        setRadius(radius) {
            this.hexagon.radius = radius;
        }

        /**
         * Changes ripple size
         * @param size
         */
        setRippleSize(size) {
            this.hexagon.rippleSize = size;
        }

        resizeCanvas() {
            this.canvas.width = document.body.clientWidth;
            this.canvas.height = document.body.clientHeight;
        }

        /**
         * Add a new hexagon that can be drawn
         * @param x
         * @param y
         * @param radius
         */
        addHexagon(x, y, radius) {
            this.hexagons.push({
                x: x,
                y: y,
                radius: radius,
                strength: 100
            });
        }

        /**
         * Draw a hexagon onto the canvas
         *
         * @param x
         * @param y
         * @param radius
         * @param strength
         */
        drawHexagon(x, y, radius, strength) {
            const a = (Math.PI * 2)/6;
            strength = strength / 100 || 0;
            this.context.beginPath();
            this.context.save();
            this.context.translate(x,y);
            this.context.moveTo(radius,0);

            for (let i = 1; i < 6; i++) {
                this.context.lineTo(radius*Math.cos(a*i),radius*Math.sin(a*i));
            }

            this.context.closePath();
            this.context.restore();
            this.context.fillStyle = "rgba(255, 255, 255, " + strength + ")";
            this.context.fill();
        }

        drawRipple(origin, rippleI) {

            // Set the initial ripple orientation
            let angleDeg = 60;

            // Determine the radius of this ripple
            const radius = (this.hexagon.radius * rippleI);

            // Set the origin of the ripple
            let coordX = (origin.x);
            let coordY = (origin.y - (radius*2) + (this.hexagon.radius * 1.5));

            if (rippleI === 1) {
                this.addHexagon(coordX, coordY, this.hexagon.radius);
                return;
            }

            // Draw each of the faces of the hexagon
            for (let face = 0; face < 6; face++) {

                // Determine the angle of this face
                const angleRad = (angleDeg * (Math.PI / 180));

                // Determine how many little hexagons should be drawn along this face
                const hexNumToDraw = ((radius / this.hexagon.radius) - 1);

                // Draw the face
                for (let i = 0; i < hexNumToDraw; i++) {

                    // Calculate the positions of each little triangle
                    const hexCoordX = (Math.sin(angleRad) * ((this.hexagon.radius * i) * 2)) + coordX;
                    const hexCoordY = (Math.cos(angleRad) * ((this.hexagon.radius * i) * 2)) + coordY;

                    // Actually draw it
                    this.addHexagon(hexCoordX, hexCoordY, this.hexagon.radius);
                }

                // Calculate the start position for the next face
                coordX += (Math.sin(angleRad) * ((this.hexagon.radius * (hexNumToDraw)) * 2));
                coordY += (Math.cos(angleRad) * ((this.hexagon.radius * (hexNumToDraw)) * 2));

                // Calculate the angle of the next face
                angleDeg -= 60;
            }
        }

        explode(origin) {

            this.drawRipple(origin, 1);
            for (let rippleIndex = 1; rippleIndex < this.hexagon.rippleSize; rippleIndex++) {
                setTimeout(_ => {this.drawRipple(origin, rippleIndex+1)}, (rippleIndex*100));
            }
        }

        drawAll() {
            this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);

            this.hexagons = this.hexagons.filter(hexagon => {
                return hexagon.strength > 0;
            });

            this.hexagons = this.hexagons.map(hexagon => {
                this.drawHexagon(hexagon.x, hexagon.y, hexagon.radius, hexagon.strength);
                hexagon.strength = hexagon.strength > 0 ? hexagon.strength - 3 : 0;
                return hexagon;
            });

            requestAnimationFrame(_ => this.drawAll());
        }
    }

</script>

<script>
    const hexplosion = new Hexplosion(document.getElementById("cont"));
    document.addEventListener('resize', e => hexplosion.resizeCanvas());
</script>

</body>
</html>
```