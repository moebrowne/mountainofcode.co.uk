# 🦨 Stinky Code Refactor

#code smell
#refactor

I came across [this article](https://laravel-news.com/request-accepts) about content negotiation in Laravel today. The 
article is okay but there was something about the examples which didn't smell right. Specifically, this method:

```php
public function details(Request $request, Product $product)
{
    $supportedFormats = ['application/json', 'text/html', 'application/pdf'];

    if (!$request->accepts($supportedFormats)) {
        return response()->json([
            'error' => 'Unsupported content type',
            'supported' => $supportedFormats
        ], 406);
    }

    if ($request->accepts(['application/json'])) {
        return response()->json([
            'product' => $product->load(['reviews', 'specifications']),
            'related_products' => $product->getRelatedProducts(5),
            'average_rating' => $product->reviews->avg('rating')
        ]);
    }

    if ($request->accepts(['application/pdf'])) {
        return response($this->generateProductPdf($product))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="product-' . $product->id . '.pdf"');
    }

    return view('products.details', compact('product'));
}
```

Trying to parse the code was jarring. The first `{php}if()` block was error checking, the following two blocks where to
handle JSON and PDF, and then it renders a view?

I think it would be much easier to parse if structured like this:

```php
public function details(Request $request, Product $product)
{
    if ($request->accepts('application/json')) {
        return response()->json([
            'product' => $product->load(['reviews', 'specifications']),
            'related_products' => $product->getRelatedProducts(5),
            'average_rating' => $product->reviews->avg('rating')
        ]);
    }

    if ($request->accepts('application/pdf')) {
        return response($this->generateProductPdf($product))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="product-' . $product->id . '.pdf"');
    }

    if ($request->accepts('text/html')) {
        return view('products.details', compact('product'));
    }

    return response()->json(['error' => 'Unsupported content type'], 406);
}
```

Each block explicitly handles one type, it's easy for the eye to quickly scan and jump between blocks. If the execution
gets to the end and all options have been exhausted an error is returned.

These changes have eliminated several ways bugs can creep in. In the original, adding a new acceptable type required the
developer to update both the error checking and implement a handler. This approach only requires the developer to
implement the handler. Easy. The same applies when removing a handler.

You could go one step further and introduce dedicated methods for each response type. In this case, I don't think it's
necessary and is veering into premature optimisation, but here it is regardless:

```php
class ProductController extends Controller
{
    public function details(Request $request, Product $product)
    {
        return match(true) {
            $request->accepts('application/json') => $this->makeJsonResponse($product),
            $request->accepts('application/pdf') => $this->makePdfResponse($product),
            $request->accepts('text/html') => $this->makeHtmlResponse($product),
            default => response()->json(['error' => 'Unsupported content type'], 406),
        };
    }

    private function makeJsonResponse(Product $product) {/*...*/}

    private function makePdfResponse(Product $product) {/*...*/}

    private function makeHtmlResponse(Product $product) {/*...*/}
}
```

The eagle-eyed among you will have noticed that the new examples aren't functionally identical to the original. The
error response is missing the `supported` array, this is good <abbr title="Developer Experience">DX</abbr>.

To support this requires completely changing approach:

```php
class ProductController extends Controller
{
    public function details(Request $request, Product $product)
    {
        $responseGenerators = [
            'application/json' => fn() => $this->makeJsonResponse($product),
            'application/pdf' => fn() => $this->makePdfResponse($product),
            'text/html' => fn() => $this->makeHtmlResponse($product),
        ];

        foreach ($responseGenerators as $type => $responseGenerator) {
            if ($request->accepts($type)) {
                return $responseGenerator();
            }
        }

        return response()->json([
            'error' => 'Unsupported content type',
            'supported' => array_keys($responseGenerators),
        ], 406);
    }

    private function makeJsonResponse(Product $product) {/*...*/}

    private function makePdfResponse(Product $product) {/*...*/}

    private function makeHtmlResponse(Product $product) {/*...*/}
}
```

I think verbose error messages would need to be a hard requirement to justify shipping this code. The additional
cognitive load on readers of the code and the trouble it would cause to static analysers dramatically undermines any
value added.
