# Thonny Not Detecting Enviro Board

#pimoroni

Today I wanted to get into a [Pimoroni Enviro](https://shop.pimoroni.com/products/enviro-indoor) board ([I love these](/fah-thermostat))
to reconfigure it. They run Micropython, and it's recommended to use [Thonny](https://thonny.org/) to connect to them to
write new firmware or just read files out. When I connected my board, it didn't show up:

```
Couldn't find the device automatically. 
Check the connection (making sure the device is not in bootloader mode) or choose
"Configure interpreter" in the interpreter menu (bottom-right corner of the window)
to select specific port or another interpreter.
```

I believe the issue is that a provisioned board will immediately go to sleep when it is powered up, waiting to take the
next reading. The way to get it to stay awake is to hold the POKE button as you plug it in.

I'm surprised this isn't documented anywhere, I probably just missed it.
