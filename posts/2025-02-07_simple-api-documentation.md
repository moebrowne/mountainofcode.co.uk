# Stupidly Simple API Documentation

#API
#documentation

I have built a couple of internal APIs for work, they were super simple, less than 3 endpoints. Nevertheless, I wanted to
add some documentation of what parameters were available.

The idea is when you visit the base URL of your API, say `/api`, you get a JSON response containing a list of available 
endpoints, like this:

```json
{
    "index": "https://example.org/api/list?startFrom={date:YYYYMMDD}&limit={integer}",
    "post": "https://example.org/api/post/{slug}"
}
```

There is some missing information, mostly for what is returned but this gives a great starting point for exploration.
An additional benefit is that developers will almost certainly be using a browser which renders the JSON in a pretty
format and allows the URLs to be clicked.

The only limitation is that it doesn't really work for POST requests but when you start doing POSTs you've probably gone
beyond 'stupidly simple'.
