# DIY Micro Geothermal

#geothermal
#project

We have a porch at the back of our house, it's glass on three sides and predominately faces south, we use it as a mini
greenhouse. Each winter we have to move all tender plants out of there because it will inevitably drop below freezing.

This got me thinking about ways to always keep it above freezing. It's definitely not important enough to pay to heat
the place, this is more about seeing if I can.

The irony of this porch was that in the height of summer it is waay to hot and then in winter too cold, ideally I
wanted to store the energy of summer for 6 months and release it in winter. I was watching an episode of Grand Designs
where they were building a house into a hillside and using the hill as an 'earth battery', doing exactly what I wanted.
Storing the summer heat for winter. I don't have a spare hillside, but I do have a garden. Could I store or extract a
meaningful amount of energy in the ground?

The idea of creating a micro-geothermal system was fascinating.


## It's Warm Down There

![Graph showing how ground temperature changes over the year](/images/UK-soil-temperature.png)

To my amazement, I discovered that in the south of the UK the soil temperature at a depth of 1m is 6-18°C year round. It
seems to depend a lot on things like soil composition and season, but it's always well above freezing. There could be
snow on the ground and the temperature would be maintained. This seemed kinda ideal, it's cold enough to cool in the
summer and warm enough to heat in the winter.

My excitement was tempered by two things: Firstly if this was as easy as it seemed then everyone would already be doing
it, second was that commercial ground source heat pump systems are super expensive and require huge boreholes or
trenches filled with miles of pipe. I wasn't completely discouraged though, I only wanted a heat/cool a small volume by
a small amount.


## How Warm?

I went out to the garden one frosty morning and dug a hole about the diameter of my arm and about 300mm deep, I stuck a
digital thermometer in the bottom of the hole, it read 10°C, the surface was ~0°C. That's something.

![geothermal test hole](/images/geothermal-test-hole.jpg)

The first question is how much heating will I need? The porch is 1x2x2m or 4m³, I have only ever seen it get to -3°C,
so lets say we need 5°C of heating. To heat 4m³ of air by 5°C requires ~7Wh, but given how poorly insulated it is and
how low the temperature difference is I think we need to double or possibly triple that number. Let's guess at 15Wh per
day.

Next I needed to figure out how much energy I could expect to extract from a micro-geothermal heat sink. I searched
online for anyone who had made a similar project, there were disappointingly few, this wasn't a good sign.

I turned to the next best thing, AI. Using the prompt: "How much energy in Watt Hours could I extract from a 2m coil of
8mm pvc tube buried 1m underground in the south of the UK over 24hrs?" gave the following answers:

- Claude 3.7: 40 Wh
- OpenAI ChatGPT 4o: 3.3 kWh
- Google Gemini 2.5 Pro (preview): 150 Wh
- Google Gemini 2.0: _refused to answer_
- Meta LLama 3.3: 0.7 Wh
- DeepSeek Chat v3: <0.1 Wh


These results range from nothing to obviously wrong. I'm not entirely surprised, there are so many variables. Guess I
will have to find out the hard way. 😁


## Experiment Time!

Next, I will build a prototype and collect some real world empirical data. I'm going to keep it super quick and dirty
and use stuff I already have lying around because I suspect this won't work at all! I've got 5m of 8mm PVC pipe, I'll
coil as much of that as I reasonably can to form the underground heat sink and bury it as deep as possible, the rest of
the pipe I'll just lay out on the porch floor. I've got a small diaphragm pump which can circulate water through the
loop. I think recording the temperature of the soil, air and water will be enough to get an idea.

My hypothesis is that I will see the underground temperature drop quickly when the air temperature is lower than the
soil temperature and vice versa.



## Prior Art

- <https://www.youtube.com/watch?v=s-41UF02vrU>
- <https://www.youtube.com/watch?v=3o1jl2Qs9L4>
- <https://www.youtube.com/watch?v=ofVLrPlro0I>
- <https://www.youtube.com/watch?v=cALbn8b6MpA>
- <https://www.youtube.com/watch?v=YTd3GwxwSPs>
- <https://www.youtube.com/playlist?list=PL5JZOBQ5tC4di-yysZwuJ0oxpAqwOJx5h>
