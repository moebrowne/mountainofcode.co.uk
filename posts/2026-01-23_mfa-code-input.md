# Multifactor Authentication Input Boxes

#MFA
#UX
#rant

If you ever implement 6-digit style MFA don't use multiple inputs. It breaks so many ways users interact with forms. The
number of times I've gone to put in an MFA code and can't do simple things like paste because the developer only ever
tested typing in the code.

There are so many ways to interact with inputs. If you try and reimplement them in JS you will miss some: copy/paste,
arrow keys, backspace, home, end, insert mode typing, click-drag to highlight some/all, double click to highlight all,
screen readers, etc, etc.
