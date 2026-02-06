# 3D Modeling Parts To Fit Existing Parts

#OpenSCAD
#3D Modeling

Most of the parts I design and print need to fit with an existing thing, be that a RaspberryPi or another 3D printed
part. I have found over the years that the best way to ensure that everything fits together well is to have a model of 
the existing part in the editor.

This is easy if you already have an STL of the part, then you can just `{openscad}import("file.stl")` it. If it's a 
well-know part, like a Raspberry Pi, then you can often find pre-made models on sites like
[Thingiverse](https://www.thingiverse.com/) or [Printables](https://www.printables.com).

|x|(/3d/reference/Raspberry Pi 4 Model B.stl)

It often takes a little while to model the existing part but overall I believe it's worth doing because it enables you
to iterate faster. You no longer need to print out a physical copy of the part only to discover that it doesn't quite
look like you'd imagined or that it would work better if this part was mounted at an angle. The editor gives you a full
view of how it will all look together.

There are also a couple of ways to shortcut the modelling process by skipping irrelevant parts or approximating them to
a cube or cylinder.

Doing this has meant I get working parts much quicker, saves a bit of filament too.

I was reminded of the value of doing this recently while designing a relatively complex part to programmatically control
a CO<sub>2</sub> valve with a servo.

![co2-regulator-openscad-model.png](/images/co2-regulator-openscad-model.png)

The part in grey is the existing valve/regulator that my new part needs to attach to. It was measured as accurately as
I could with calipers. The part in green is the new part.

The parts were accurate enough that I was able to do a `{openscad}difference()` between the existing part and the new
part which automatically put all the holes and cutouts in exactly the right place.
