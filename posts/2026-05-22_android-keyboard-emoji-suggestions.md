# Android Keyboard Emoji Search

#emoji
#android


I want to use as little Google software as I can get away with. This goes as far as things like the Android GBoard on my
phone. I want an opensource keyboard, and there are plenty. None of the ones I tried seem to have an emoji search. This
sounds really minor, but given how many emojis there are to scroll through, the only way to find them is by search.

[FlorisBoard](https://florisboard.org/) came the closest. It can suggest an emoji when you type its name, for example,
typing "confetti" would suggest 🎊. This was OK, but it wasn't great, where it fell down for me was that it only matched
on exact strings, typing "smile" only suggested 😼 because ☺️ is "smiling face".



## Personal Emoji Dictionary

The solution was to use a "personal dictionary". I could add 😁 (Beaming Face with Smiling Eyes) and set it as a synonym
for "grin" and "smile". This dictionary is agnostic of the keyboard app, so I was free to pick based on other criteria.
I picked [HeliBoard](https://github.com/HeliBorg/HeliBoard).

The problem now is that there are about 2,000 common emojis... I only use a fairly small number of them, but the idea of 
manually entering even most of them through the Android UI didn't sound like fun. Time for some automation 🤖

I needed to figure out where the dictionary was stored on the phone. I started with the file explorer app, don't know
why I bothered, it's useless. Next I connected via [ADB](https://developer.android.com/tools/adb) and was able to find
it. It's an SQLite DB:

```
/data/data/com.android.providers.userdictionary/databases/user_dict.db
```

`{sql}SELECT * FROM words`

```
1|🎉|250|en_GB|0|tada
2|😁|250|en_GB|0|grin
3|😁|250|en_GB|0|smile
```

To generate the entries, threw the problem at Claude and got a decent CSV, which I could then import into SQLite. It was
a long way from perfect, but it was good enough after some fine-tuning for to my personal taste.

For reference, the locally created DB was copied to the phone using ADB:

```
adb push emoji_en.dict /data/data/com.android.providers.userdictionary/databases/emoji_en.dict
```
