# 📹 OctoPrint Camera Mount

#3D model
#OctoPrint

I don't print a lot of things which take many hours, I only have a small printer, but when I do, or if it's a risky
print, then it's annoying to have to stop what you're doing to go and check on it. This is also a solved problem: add a 
camera. [OctoPrint](https://octoprint.org/) has supported a webcam feed since forever.

I already had a camera lying around which I wanted to use. I couldn't find an existing mount which worked for my
camera/printer combo, so I sat down for a couple of hours and designed one in [OpenSCAD](https://openscad.org/). It took
a number of attempts to a good design with the correct angle. This is what I ended up with:

![](/images/octoprint-camera-mounted.jpg)

![](/images/octoprint-camera-iterations.jpg)

The slot in the middle is for the ribbon cable to pass through to the Pi mounted behind.

The print bed is fully visible, at least when the print head is retracted. It's an <abbr title="Infrared">IR</abbr>
camera, so the colours look off, but that doesn't bother me. It means I can check on it in the dark.

![](/images/octoprint-camera-view.png)

An [Adafruit PCB ruler](https://www.adafruit.com/product/1554) made for a great target to adjust the focus.


## The Model

|x|(/3d/octopi-camera-mount.stl)

```openscad
+(/public/3d/octopi-camera-mount.scad)
```


- [Download STL](/3d/octopi-camera-mount.stl){download=true}
- [Download SCAD](/3d/octopi-camera-mount.scad){download=true}
