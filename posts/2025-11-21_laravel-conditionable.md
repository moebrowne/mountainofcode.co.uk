# Always Optimise For Readability

#Laravel
#DX

[This article](https://www.harrisrafto.eu/clean-conditional-logic-with-laravels-fluent-conditionable-trait/) came across
my feeds recently, it talks about Laravel's Conditionable trait which in their words:

> The `Conditionable` trait enables you to conditionally execute operations on an object using a fluent interface.
> Instead of breaking your chain of method calls with traditional if-else statements, you can use the `when()` and
> `unless()` methods to create code that reads almost like a natural language sentence.

It shows the following, shortened, example:

```php
Fluent::make([
    'id' => $user->id,
    'name' => $user->name,
    'email' => $user->email,
    'joined_at' => $user->created_at->toDateString(),
])
    ->when($request->user()->can('view-statistics'), function (Fluent $data) use ($user) {
        return $data->set('post_count', $user->posts()->count())
                    ->set('comment_count', $user->comments()->count());
    })
    ->when($user->isVerified(), function (Fluent $data) {
        return $data->set('verified', true)
                    ->set('verification_date', $user->verified_at->toDateString());
    })
    ->unless($request->includesPersonalData(), function (Fluent $data) {
        return $data->forget('email');
    })
```

Frankly, I think this is an unreadable mess.

I know that code style is polarising, personal preference, and that there is no right-or-wrong, buuuut... The line
length is too long, each line is doing too many things, and it's hard to scan. I had to carefully re-read it to figure
out what was going on.

Personally, I would have written something like this:

```php
Fluent::make([
    'id' => $user->id,
    'name' => $user->name,
    'email' => $user->email,
    'joined_at' => $user->created_at->toDateString(),
])
    ->when(
        $request->user()->can('view-statistics'),
        static function (Fluent $data) use ($user): Fluent {
            return $data
                ->set('post_count', $user->posts()->count())
                ->set('comment_count', $user->comments()->count());
        },
    )
    ->when(
        $user->isVerified(),
        static function (Fluent $data): Fluent {
            return $data
                ->set('verified', true)
                ->set('verification_date', $user->verified_at->toDateString());
        }
    )
    ->unless(
        $request->includesPersonalData(),
        static function (Fluent $data): Fluent {
            return $data->forget('email');
        }
    )
```

To my eye it's now much easier to scan through and find the part you're actually interested in working on. The trouble
is when you compare it to using 'traditional if-else statements', you have to wonder what you're gaining:

```php
$data = Fluent::make([
    'id' => $user->id,
    'name' => $user->name,
    'email' => $user->email,
    'joined_at' => $user->created_at->toDateString(),
]);

if ($request->user()->can('view-statistics')) {
    $data = $data
        ->set('post_count', $user->posts()->count())
        ->set('comment_count', $user->comments()->count());
}

if ($user->isVerified()) {
    $data = $data
        ->set('verified', true)
        ->set('verification_date', $user->verified_at->toDateString());
}

if ($request->includesPersonalData() === false) {
    $data = $data->forget('email');
}
```

Is this so bad? Why is "reads like a natural language" something that developers seem to desire?



## So Never Use It Then?

Like every tool, there are times when it's useful, for me those times are when the condition is simple and the operation
fits into a short closure. The `{php}unless()` call in their example is a good candidate for this, I'd write this:

```php
Fluent::make([
        'name' => 'Michael Nabil',
        'developer' => true,
        'posts' => 25,
    ])
    ->unless(
        auth()->isAdmin(),
        static fn (Fluent $input): Fluent => $input->forget('posts')
    );
```

It's short and to the point and crucially optimises for readability. For anything longer just break the chain.



## Take A Step Back

I only focused on the use of Conditional, that was the point of the post after all. However if you look at the intent of
the code, it becomes clear that the whole thing is being overengineered. All we need is:

```php
$data = [
    'id' => $user->id,
    'name' => $user->name,
    'joined_at' => $user->created_at->toDateString(),
];

if ($request->user()->can('view-statistics')) {
    $data['post_count'] = $user->posts()->count();
    $data['comment_count'] = $user->comments()->count();
}

if ($user->isVerified()) {
    $data['verified'] = true;
    $data['verification_date'] = $user->verified_at->toDateString());
}

if ($request->includesPersonalData()) {
    $data['email'] = $user->email;
}
```

No closures, no classes, no double negatives for the email key, even the most junior developer would understand this.
KISS.



## A Note On Performance

Calling a closure is only ~free. The overhead is tiny, but if you're leveraging the Conditional trait inside tight
loops or performance-sensitive paths in your code, you may want to benchmark it.
