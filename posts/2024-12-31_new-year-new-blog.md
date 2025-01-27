# New Year New Blog 🎆

#meta
#redesign

Part of the reason there I post so infrequently is that it's such a PITA to publish anything to. It's taken several 
forms over the years, from a [Ghost blog](https://ghost.org/), to a static site, to a homegrown markdown blog generator,
to a [Filament app](https://filamentphp.com/). Nothing really stuck.

I sat down and made a list of all the things which needed to change, did a quick mock up design in dev tools, slept on
it and came back to it the next day.

One thing that became clear was the need for simplicity. The bare minimum required to publish a post needed to be a
single standard markdown file, no `meta.json`, no front matter, no intros. The less I need to remember the better.

## Requirements

- Has to be low effort to add/edit - must be easy to add via GitHub UI
- No compilation or build steps
- Git deployment - server poll
- Single directory of markdown files
- Use `#tag-name` to set post tags
- The first (and only) H1 is the headline of the post
- The homepage is just a list of titles and dates, no pagination, date ordered
- The date is in the filename: `2024-10-10_name.md`
- The post URL is the markdown filename (minus the date)
- Remove the tags sidebar
- Remove the header images, pretty but too much effort
- Update background to show corrupt cells from the start
- Use [league/commonmark](https://commonmark.thephpleague.com) parser
- Use [tempest/highlight](https://tempestphp.com/docs/highlight/getting-started/) for the code block highlighting
- Must work with browser reader mode

It took maybe 4 hours to make all the changes. It was important to make the list up front, If I had made the
changes as I went I'm sure I would've gotten side tracked and not actually shipped anything.

I don't know if this will mean I actually write more, probably not If I'm honest, but the barrier to entry is now
significantly lower.

## Performance

| Metric                             | Before | After | Change |
|------------------------------------|--------|-------|--------|
| Page Speed - Mobile Performance    | 98     | 98    | -      |
| Page Speed - Mobile Accessibility  | 63     | 100   | +58%   |
| Page Speed - Mobile Best Practice  | 100    | 100   | -      |
| Page Speed - Mobile SEO            | 83     | 100   | +20%   |
| Page Speed - Desktop Performance   | 100    | 100   | -      | 
| Page Speed - Desktop Accessibility | 63     | 100   | +58%   | 
| Page Speed - Desktop Best Practice | 100    | 100   | -      | 
| Page Speed - Desktop SEO           | 83     | 100   | +20%   | 
| Apache Bench - req/s               | 6.9    | 8.1   | +20%   |
| Apache Bench - p95                 | 330ms  | 291ms | +10%   |


## Before & After Screenshots

![Before](/images/homepage-before.png)

![After](/images/homepage-after.png)

![Before](/images/post-before.png)

![After](/images/post-after.png)




