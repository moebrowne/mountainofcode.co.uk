# HTML Custom Elements

#HTML

I really like HTML custom elements AKA web components and don't use them enough. They don't even have to implement any
functionality to add value.

I'll use them just to make code easier to scan. For example:

```html
<div class="sidebar">
    <div class="side-nav">
        <div class="nav-item"></div>
    </div>
</div>

<!-- vs -->

<side-bar>
    <side-nav>
        <nav-item></nav-item>
        <nav-item></nav-item>
        <nav-item></nav-item>
    </side-nav>
</side-bar>
```


## Naming

Custom element names must contain a `-`, this is to prevent collisions with future HTML elements. This isn't a big deal,
and it's pretty common now to prefix custom elements with `x-`. Eg `{html}<x-sidebar>`.



## Inline By Default

Custom elements are `{css}display: inline` by default. I believe it's like this for backwards compatability reasons.
This is pretty annoying. Until very recently, I would add a bunch of CSS like this to override it:

```css
custom-element0 {display:block}
custom-element1 {display:block}
custom-element2 {display:block}
custom-element3 {display:block}
```

There is a better way though:

```css
*:not(:defined) {
    display: block;
}
```

This makes all 'unregistered' custom elements `{css}display: block`. From the [MDN docs](https://developer.mozilla.org/en-US/docs/Web/CSS/:defined):

> The :defined CSS pseudo-class represents any element that has been defined. This includes any standard element built
> into the browser and custom elements that have been successfully defined (i.e., with the `CustomElementRegistry.define()`
> method).

The last part is important. If you register the element, you'll need to manually set the display value.


