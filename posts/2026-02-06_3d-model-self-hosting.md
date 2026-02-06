# Self-Hosting My 3D Models

#3D
#self hosting

I've designed a number of 3D models since I got a 3D printer and [discovered OpenSCAD](/3d-modeling-for-developers).
Most of them were pretty niche, but I wanted to have a public place to share the code/documentation/notes/etc.

I started by putting the models on Thingiverse and then into a GitHub repo. Both were a faff, which meant I didn't
bother. I also didn't really like that I was always using someone else’s servers. I wanted something easier, more
engaging, more fun and under my control.

Being a developer, I obviously turned to building a little app. I wanted it to be simple and set about making a list of
requirements:

- Easy to add to - Low barrier to entry
- Index is a grid of images, not even a title
- Automatically generate an image from the OpenSCAD source
- 3D viewer of the STL
- STL download
- Inline OpenSCAD source
- Short description
- Optional IRL pictures


I [let the idea simmer](/letting-ideas-simmer) for a bit and got building. I had a couple of goes at it, but something
didn't feel right. I stepped back and realised that I was rebuilding this blog, the 3D model viewer, Markdown parsing,
code highlighting, image sizing, etc.

![](/images/3d-models-template.png)

It took me a couple of days to realise I didn't need another app. Each model would fit nicely as a blog post.

The time I'd spent wasn't wasted. Most of the work was in the templates and the [OpenSCAD thumbnail renderer](/rendering-openscad-to-image)
both of which were used.

It took a couple of hours to collect all the models together and create the posts but it felt good to have them all in
one place. I also loved that there is now at least one post per year since I started the blog back in 2015. All the new
posts are tagged with `<3D MODEL>`, and there is a [gallery](/3d) which links to each post.

