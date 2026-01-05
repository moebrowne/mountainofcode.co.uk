# Prefers Reduced Motion

#A11Y

The CSS media query `prefers-reduced-motion` has been around for years, it indicates to a website that the user would
rather not see animations.

I've never really had a real reason to put it into action, at least I thought I didn't. Turns out there are two places
on this very blog where there is some kind of default motion: the background and the [embedded 3D model viewer](/3d-modeling-parts-which-fit-with-an-existing-part).

Both of these are now static if requested. In both of my cases I had to use JS to detect the preference:

```js
window.matchMedia('(prefers-reduced-motion: reduce)').matches === true
```

As always, there's more info on the [MDN Docs](https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/At-rules/@media/prefers-reduced-motion)
