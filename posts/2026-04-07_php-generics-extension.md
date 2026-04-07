# PHP Generics Extension

#PHP
#generics


I'm a believer in adding as much type information as you can when programming. It gives IDEs and static analysis tools
so much power to find bugs or give useful suggestions.

I work with PHP, which has historically been a very loose language. It's gotten better over the years and now has typed
properties, return types, unions, intersections, <abbr title="Disjunctive Normal Form">DNF</abbr>, etc.

What we don't have is generics:

```php
class Thing<T> {
    private array<T> $widgets;
}

new Thing<Widget>();
```

It gets talked about a lot, and the PHP Foundation did some [research](https://thephp.foundation/blog/2024/08/19/state-of-generics-and-collections/)
into the options a couple of years ago. I believe that the only viable way forward for PHP is erased generics paired
with a separate static analysis tool like [PHPStan](https://phpstan.org/). This is basically what TypeScript is.

I didn't want to create a separate transpiler tool like TypeScript, I wanted to keep the same <abbr title="Developer Experience">DX</abbr>
PHP devs are used to. I wondered if erased generics could be achieved via an extension.

I've written very little C in my time, let alone a whole PHP extension, but with the help of [Claude](/i-love-claude)
and the [PHP Internals Book](https://www.phpinternalsbook.com/), I had a working extension in a couple of hours. It
strips out the generics syntax on the fly at runtime. It supports classes, methods, properties, functions, closures,
nesting, namespaces, etc:

```php
class Container<T> {
    public array<Widget>|null $widgets;
    public Collection<Widget|Thingy> $widgetsOrThingys;
    public Factory<Maker<Widget>|null> $nesting;

    public function __construct(
        public array<Widget>|null $data,
    ) {}

    public function find(): array<Widget>|null {}
}

new Container<Widget>();

function foo(array<\App\Models\Widget> $items) {}

$closure = function(): array<Widget> {};
```

Code and installation instructions are in the [repo](https://github.com/moebrowne/erased-generics), just remember it's
very much a prototype to explore what's possible, there will be bugs 🦗

[![](https://opengraph.githubassets.com/a1ee28522bce5623e15e51536c0825f815e2bcdc202eabbcb2307c05027bc677/moebrowne/erased-generics)](https://github.com/moebrowne/erased-generics)



## What's Next?

It needs a shed load more testing, and to really be useful it needs an accompanying PHP Storm plugin. This isn't going
to work:

![](/images/php-generics-phpstorm-error.png)




## Links

- <https://thephp.foundation/blog/2024/08/19/state-of-generics-and-collections/>
- <https://www.youtube.com/watch?v=K8r-WooX49E>
- <https://stitcher.io/blog/generics-in-php-1>
- <https://stitcher.io/blog/generics-in-php-2>
- <https://stitcher.io/blog/generics-in-php-3>
- <https://stitcher.io/blog/generics-in-php-4>
- <https://stitcher.io/blog/the-case-for-transpiled-generics>
- <https://stitcher.io/blog/php-generics-and-why-we-need-them>
- <https://wiki.php.net/rfc/generic-arrays>
- <https://wiki.php.net/rfc/generics>
- <https://github.com/PHPGenerics/php-generics-rfc/issues/45>
- <https://phpstan.org/blog/generics-in-php-using-phpdocs>
