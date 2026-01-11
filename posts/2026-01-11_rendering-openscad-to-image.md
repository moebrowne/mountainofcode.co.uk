# 📸 Rendering OpenSCAD Models To PNG

#OpenSCAD
#3D

I'm working on a project where I need to generate transparent images of [OpenSCAD](https://openscad.org/) models. Easy,
right? OpenSCAD can directly export PNGs. As always, the devil is in the detail. My approach is inspired by [this post](https://calbryant.uk/blog/pushing-openscad-to-the-max-with-discipline-and-imagemagick/#for-3d-renderings)
which is doing a lot more than I need.


## Antialiasing

![](/images/openscad-render-aliasing.png)

The native PNG export produces some pretty jagged edges. We can work around this by exporting a huge image and then
shrinking it down.

```bash
openscad-nightly \
    --hardwarnings \
    --autocenter \
    --viewall \
    --imgsize=4096,4096 \
    --render \
    -o model.png \
    model.scad

convert \
    model.png \
    -resize 700x700 \
    model.png
```


## Transparency

![](/images/openscad-render-transparency.png){class="checkerboard-background"}

Making the background transparent was a lot easier than I expected. Image Magick can do a colour-to-transparency mask.
This replaces the background colour of `#fafafa` used in the Nature colour scheme. I also much prefer this colour
scheme.

```bash
openscad-nightly \
    --hardwarnings \
    --autocenter \
    --viewall \
    --imgsize=4096,4096 \
    --colorscheme Nature \
    --render \
    -o model.png \
    model.scad

convert \
    model.png \
    -transparent "#fafafa" \
    -resize 700x700 \
    model.png
```


## Padding

The exported images contain a lot of whitespace around them, again we can lean on Image Magick to strip it back:

```bash
openscad-nightly \
    --hardwarnings \
    --autocenter \
    --viewall \
    --imgsize=4096,4096 \
    --colorscheme Nature \
    --render \
    -o model.png \
    model.scad

convert \
    model.png \
    -transparent "#fafafa" \
    -trim \
    -resize 650x650 \
    -bordercolor none \
    -border 25 \
    model.png
```


## Camera Position

![](/images/openscad-render-camera.png)

The default camera position is usually fine, but sometimes you need to adjust the camera position. The `--camera`
argument does this but don't try to manually figure out the numbers to pass. Instead, position the model in the GUI and
copy the numbers from the toolbar:

![](/images/openscad-render-camera-numbers.png)

```bash
openscad-nightly \
    --hardwarnings \
    --autocenter \
    --viewall \
    --imgsize=4096,4096 \
    --colorscheme Nature \
    --camera 3.12,-1.71,-2.69,73.20,0,32,192.04 \
    --render \
    -o model.png \
    model.scad
```


## Multiple Parts

It's not unusual for a design to consist of multiple parts, and I want each part to have its own image. The solution I
came up with was to add <magic-sparkle>magic comments</magic-sparkle> at the end of the file. Each one is a snippet
which defines how to render a part. Replacing `//part: ` with `!` will render just that part.

```openscad
module base() { /* ... */ }
module nozzle() { /* ... */ }

//part: base();
//part: nozzle();
```


## Automation

Of course, I'm not doing all this manually. I whipped up the following script which processes all the models in a
directory. It includes STL rendering too.

```php
+(/render-scads.php)
```
