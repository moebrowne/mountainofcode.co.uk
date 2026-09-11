# Creating 3D Models From Paper Drawings

#3D Printing
#OpenSCAD


I needed a stand for a tablet, there are thousands of options online, but none of them were quite right, I wanted
something specific. That's the joy of 3D printing, right? Usually this would mean opening up [OpenSCAD](https://openscad.org/)
and creating a model, but I wanted something natural, and heavily curved.

I could physically draw the outline of what I wanted, take a picture of it and convert that into a black and white image
which could then be imported into OpenSCAD with the `{openscad}surface()` function.

![](/images/stand-paper.jpg)

I included a ruler in the image for scale, this turned out to not be necessary.

I opened the image in [GIMP](https://www.gimp.org/) and traced it with pure black. Not I had a high-contrast black and
white image. This is what it looked like in OpenSCAD once I had messed about with the scale:

![](/images/stand-attempt1.png)

Looks great right? Well, I printed it, and it's not. It came out ok, but the printer was not happy about it, it took
ages, made a lot of noise, vibrated a lot, and the faces were kinda rough. Zooming in on the model reveals the problem:

![](/images/stand-aliasing.png)

There were thousands of these ridges, all caused by aliasing in the image. I think this problem is fundemental to the
approach I took. An image will always have pixels... A few weeks went by not really knowing what to do about this...
Then I had the idea to convert the image to an SVG. SVGs don't have pixels and OpenSCAD can import them directly into 2D
shapes.

There are lots of online converters which will take a PNG and take a stab at converting it into an SVG. [This one](https://tracesvg.com/)
worked for me, but your mileage may vary. I think having a pure black and white image helped a lot here.

![](/images/stand.svg)

![](/images/stand-attempt2.png)

That's more like it
