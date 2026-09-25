# Binary Endianness When Written

#TIL
#binary

I always believed that a written binary representation, like `10010`, was ambiguous due to endianness. Turns out this is
not the case. The most significant bits always come first, `10010` is 18.

[Endianness](https://en.wikipedia.org/wiki/Endianness) defines byte order for data in memory, storage, and transmission,
not when written for humans.
