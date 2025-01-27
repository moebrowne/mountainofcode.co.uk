# 📊 Charts In Markdown

#Markdown
#Chart

I wanted to be able to add charts to my posts, previously I'd just included an image but I thought I could do better
using inline SVGs. Markdown understandably doesn't include syntax for rendering charts, but I thought it should be possible.
The requirements were simple:

- Super easy to edit
- Rendered as SVGs
- The chart code was in the Markdown - no image or iframe nonsense.
- IDE integration/highlighting.

The SVG rendering was easily done using the [pierresh/simca](https://github.com/pierresh/simca) library. The Markdown
integration was more challenging.

My first idea was to add custom syntax, or more specifically extend the table syntax, something like this:

```
|~ Time | Temp 1 | Temp 2 |
|-------|--------|--------|
| 1     | 14.98  | 2      |
| 2     | 14.04  | 3      |
| 3     | 12.86  | 4      |
| 4     | 1.32   | 6      |
```

This checked the boxes for easily to edit thanks to PHPStorm's UI, but it didn't allow any customisation of the chart,
for example selecting bar vs line chart, trend lines, axis labels etc.

My next idea was to include PHP code directly in the Markdown source and before rendering it to HTML pass it through the
PHP parser using output buffering and `{php}eval()`. This didn't include any IDE integration, would have problems if I 
had any posts which included PHP code blocks and just [smelled bad](https://en.wikipedia.org/wiki/Code_smell)...

A little while later I remembered a project I had stumbled upon called [Runmd](https://github.com/broofa/runmd), it
introduced the idea of code blocks in Markdown being executable.

This gave me the idea to have normal code blocks, which gave me the IDE integration and highlighting, but to include a
special comment. This is parsed out and executed, the output of the code is injected straight into the response.

```php
```php
//​[eval]
echo new \Pierresh\Simca\Charts\LineChart(700, 300)
    ->setSeries([ [1, 3, 9, 27, 81] ])
    ->setLabels([ '0900', '0915', '0945', '1000', '1015' ])
    ->render();
​```
```

Now the above markdown turns into:

```php
//[eval]
echo new \Pierresh\Simca\Charts\LineChart(700, 300)
    ->setSeries([ [1, 3, 9, 27, 81] ])
    ->setLabels([ '0900', '0915', '0945', '1000', '1015' ])
    ->render();
```

I would have liked to use a different language tag, like `php --eval`, rather than a special comment but then the all
important IDE integration was lost, the comment is fine.

This approach has some side benefits I didn't anticipate at first. For example because it is processed before the
Markdown is parsed I could use it to dynamically generate Markdown, like generate a gallery of images from a directory.

I considered displaying the raw code via a `{html}<details>` element, hidden by default. For charts this would be
an easy way to share the raw data but I think a CSV export link would make more sense. If you really want to see the
code then it's all open source anyway :)
