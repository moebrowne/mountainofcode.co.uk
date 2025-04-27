# 3D Modeling For Developers

#3D Modeling
#OpenSCAD

When I first got a 3D printer I was excited about being able to create all kinds of custom parts for my projects.
Initially I was content with downloading and printing cool tat from Thingiverse but that didn't last long, I wanted to
make my own stuff.

Searching for 3D modeling software compatible with Linux lead to [FreeCAD](https://www.freecad.org/), this was a few
years ago there are many more options now. I watched a bunch of tutorials and read examples but it was such a steep
learning curve. It felt like I was having to learn to use a 5-axis milling machine when all I wanted to do was make a
straight cut in a piece of softwood.

I tried to push through and get over the initial hump but I just got more and more frustrated with it. It wasn't for me.

A week or two later while I was causally browsing HackerNews I happened to come across an article on
[OpenSCAD](https://openscad.org/). At first I was sceptical, the FreeCAD frustration was still raw, it seemed very 
simple, too simple. What immediately clicked for me was that it was declarative, you write code to generate 3D models:

```openscad
cube([20,20,10]);

translate([0,0,10]) cylinder(d=20, h=10);
```

![OpenSCAD Example](/images/openscad-example.png)

This was perfect! I was immediately able to start designing things, code is how my brain works. It felt fairly limiting 
at first but reading through the [official cheatsheet](https://openscad.org/cheatsheet/index.html) showed that it was
really powerful. Beyond the simple primitives like cube and cylinder there is logical flow control, loops, functions,
modules, etc.

It also fit really nicely with workflows I was already using like Git! I've since designed a bunch of useful parts, I
created a [repo](https://github.com/moebrowne/3D-models) which has most of them in.


## Update (18 Feb 25) - Replicad

I recently discovered [Replicad](https://studio.replicad.xyz/workbench) which is similar to OpenSCAD but it uses
Javascript and runs in the browser. I haven't used it yet but I'd like to give it a go, the demo shows that it is
capable of making natural curves which isn't something OpenSCAD is good at.


## Update (7 Mar 25) - OpenSCAD In The Browser

There is now an [online version of OpenSCAD](https://ochafik.com/openscad2/). It uses WebAssembly, which is very cool,
if you want to see the source it's available on GitHub: [openscad/openscad-playground](https://github.com/openscad/openscad-playground)
