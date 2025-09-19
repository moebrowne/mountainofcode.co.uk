# I ❤️ Claude

#AI


I've been using AI at home and at work pretty much daily since Mar 2025, I've consumed over 16M tokens to date. This
post is about how I got to where I am now.

To begin with, I was pretty hesitant about the whole thing, it felt like it was all moving too fast. I was mostly
concerned with privacy and knowing how the data I sent was being used. This basically boiled down to running the models
locally. This sucked. The best I could run was one of the smallest Deekseek models, this was impressive but not useful.

Where things really turned a corner for me was [Kagi's Assistant](https://help.kagi.com/kagi/ai/assistant.html). I've
used Kagi as a search engine for a while and really liked their privacy first attitude so when they released their
AI assistant, which had guarantees about data privacy, I gave it a go. It was awesome.

The main thing I liked about it was that you got access to loads of different models. You could try them all, give each
the same prompt and compare. At the time of writing, there are 32 different options and new ones appear all the time.
The second big benefit was that it could access content from the web, and not just the web in general, it respected my
[result personalisation](https://help.kagi.com/kagi/settings/personalized-results.html).

Anthropic's Claude Sonnet model quickly became my default. It seemed to give the best output for the technical type of
stuff I do. It's not infallible, none of the models are. You need to treat the output with a healthy level of
scepticism, but you would with any code or advice you got off the internet right?

Most notably, I've used Claude to write most of the code for my [YouTube aggregator](/youtube-player-rebuild), debug
weird, hard to debug Kubernetes Ingress issues and port a number of terrible BASH scripts to PHP.

I want to emphasise that **none of the content on this blog is AI written**, and it never will be, I blog because I
enjoy it.


## Notes and Observations

- Iteration is important, start small and build don't start with all the requirements - give examples eg DTOs
- Seems to work best when all the relevant code fits into the input - couple of classes hundreds of lines of code
- Really useful to quickly validate an approach or find alternatives
- Very long conversations will reach a point where the output no longer fits and context will start to get dropped - the
  conversation quickly goes south at this point.
- Pair programming iterative approach is good. It will write code based on my instruction, I'll edit it, paste it back
  in and then continue getting it to make changes
