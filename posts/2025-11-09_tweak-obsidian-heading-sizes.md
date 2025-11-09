# Tweaking Obsidian Heading Sizes

#Obsidian
#CSS

I use [Obsidian](https://obsidian.md/) most days for all sorts of different things from project tracking to data
collection to general note-taking. I have found that the default heading sizes make it a little tricky to tell between
the difference between the heading sizes, especially when quickly scanning.

![](/images/obsidian-headings-before.png)

Initially I went browsing through the themes to see if there was one which had better font sizing, but it turns out
there is a better way. Obsidian uses CSS to style its UI and has a [CSS Snippets](https://help.obsidian.md/snippets)
feature for doing exactly the kind of thing I wanted:


```css
body {
    --h1-size: 1.80em;
    --h2-size: 1.50em;
    --h3-size: 1.12em;
    --h4-size: 1.12em;
    --h5-size: 1.12em;
    --h6-size: 1.12em;
}
```

![](/images/obsidian-headings-after.png)
