# Iron Bloom - Rendering & Serialisation

#project
#devlog


## JS Rendering

The [initial plan](/iron-bloom) was that the backend would render everything to an image and send it to the browser. It became
clear that this isn't going to work when Iain created an awesome animated bonfire sprite. Animated sprites obviously
aren't going to work in a JPEG/PNG.

You could maybe do some nonsense by rendering the whole thing to a looping GIF, but that sounds like a whole load of
bugs and just no fun.

Instead, we changed to the classic render to a `{html}<canvas>` with JS approach. This requires that the server has some
way of sending the current state to the client. We needed a serialiser.



## Serialising Game State

The obvious way to do this would be JSON. Something like:

```json
[
    {
        "definition": "Tile",
        "position": {"x": 1, "y": 2}
    },
    // ...
]
```

Boooooring 😆 We're building this for fun, let's reinvent a new, better, wheel!

My first attempt was a sort of ASCII rendering of the state:

```
[W][W][W][W][W][W][W][W]
[W][F][F][F][F][F][F][W]
[W][F][F][F][F][F][F][W]
[W][F][F][F][F][F][F][D]
[W][W][W][W][W][W][W][W]
```

Each of the three primitives (tile, entity and modifier) were assigned a unique letter. Tiles where wrapped in square
brackets, `[F]` (floor), entities in curly braces, `{text}{D}` (door) and modifiers were nested inside the relevant
tile/entity like this: `{text}{D+l}` (door with a lock).

Each primitive also needed a unique ID. I used a `#` to denote an ID: `[W#41]`. Without the ID it wasn't possible for
the client to communicate with the backend about an entity. e.g. pickup item #41.

This totally ruined the ASCII-art aesthetic:

```
[W#yekr6tB2][W#eNf1i48n][W#DFe8VytZ+ms#5t4KinIp][W#Hkh2TxdL][W#gE780Mc1][W#gF9nYf3j][W#0fiw86f3][W#vPm0zT1x+ms#ETzGJPVa][W#1TDjcbBW]
[W#B63LRZYN+{D#SQzyOcKM+x#hkwBYdOc}][F#49M0nbmc][F#alZ3xQmM][F#uaX1M8L8][F#4HJYn0Hz][F#HFUNR7WW][F#qhVySvDo][F#EcSagTmn][W#aJLGmGYc]
[W#rM1aQatn][F#c1ht8X3H][F#pjNXBDjD][F#qvzLQ9jw][F#vZrSqimK+{@#aQZo93BT}][F#lZXXBEEV][F#xPXwZa0b][F#gKivBapr][W#5RyHcTZj]
[W#hMw31W0o][F#AAoerEgt][F#2POhmpmo][F#7Ua7GsYF][F#35yhsp37][F#JtQ5Zfbk][F#Z627sD3Y][F#WeEwyqJk][W#oDA3r2Ea]
[W#skgFCtn2][F#3sGJOvJw][F#pQQUCkQF][F#NQwtqwEL][F#r5u4HUYV][F#MWolePVq][F#eMs3fjVt+{C#NsQjmQuv+{K#JmM7oabJ}}][F#z5vmG8Ib][W#bHCg65qL]
[W#7nVma53b][F#n32SzzY8][F#CYsWyBI5][F#AcmCuWT1][F#3pX80Tar][F#BeDUTtYl][F#VCqqSpRj][F#iw8oRhBW][W#NX7uc25E+ms#ljAoAHk3]
[W#MhjCnsGw][F#wYsxzbx4][F#yL7T12Wa][F#CzYY3BVa][F#u3PRUV5G][F#KvcG9TP8][F#0xo0tph8][F#KK0fe0P7][W#hPS8dfj6]
[W#KWerlIc8][W#8LONkYnn][W#HCOttHo4][W#qI0p1DMi][W#joiMScex][W#OhaLTFOt][W#fDLqMBky][W#x0oXz1Ox][W#5fHTFyl1]
```

I could get over that, but the recursive nesting proved to be a PITA to both encode and decode.

I took a break for a while... 😖



## Take II

Returning to the problem, I realised that I'd gotten hung up on encoding the tiles’ positions by where it appeared in
the text. It's _much_ simpler to have one line per primitive and for each primitive to be given an explicit location:

```
[W#wallid(0,0)]
```

In the case of nested entities, the position is a reference to an ID:

```
{D#doorid(#wallid)}
```

This avoided the need for filler 'void' tiles, any coordinate which doesn't have anything in is naturally empty.

Another simplification which fell out of this refactor was that having separate tile and entity representations was
redundant. Everything is now an entity or modifier, allowing the brackets to be dropped from the state:

```
W#wallid(0,0)
D#doorid(#wallid)
```

We still needed some way to distinguish between entities and modifiers. This was achieved through convention: uppercase
letters for entities, lowercase for modifiers.



## The Final Schema (For Now)

One primitive per line, in the form:

<div class="full-bleed" style="font-family: monospace; font-size: 14px; font-weight: bold; background-color: whitesmoke; padding: 10px 0; text-align: center;">
<span style="color: firebrick">DEFINITION</span><span style="color: darkgreen">#IDENTIFIER</span><span style="color: steelblue">(LOCATION)</span><span style="color: rebeccapurple">&lt;FLAVOUR_TEXT&gt;</span>
</div>

- `DEFINITION`: One or more letters
- `IDENTIFIER`: An alphanumeric globally unique string
- `LOCATION`: Absolute coordinates `0,0` or ID reference `#idstring`
- `FLAVOUR_TEXT`: Arbritary text. Used for labels/names/etc. Optional

**Definitions**:

- `W`: wall
- `L`: liquid
- `F`: floor
- `@`: player
- `D`: door
- `C`: chest
- `K`: key
- `B`: bonfire
- `LP`: lamp
- `WP`: weapon
- `ms`: moss
- `vn`: vines
- `x`: locked
- `o`: opened
- `em`: empty
- `sh`: shiny
- `b`: burning
- and many more...



### Full Example

```
F#VLY3azY8(0,1)
F#mWWiIpF2(0,2)
F#ExZCMy5F(0,3)
F#8FgNyu3X(0,4)
F#TiF7b29w(1,4)
F#6WP0Nb0N(2,4)
F#fwiK3vft(2,3)
F#UFdDniSG(3,3)
F#l70Db9PD(4,3)
F#kGaSgyMv(5,3)
F#OozTmCxn(5,4)
F#sTz9wIW0(6,4)
F#duN7Cmob(7,4)
F#bpvEHxFZ(8,4)
F#0CcL3p9v(8,5)
F#xO8f2MOc(9,5)
F#Y8zwPQNM(9,6)
F#BUHB8wSl(9,7)
F#NvASc104(8,7)
F#qoaC92ga(7,7)
F#mhnyzdWj(7,6)
F#hgVRvknK(8,6)
F#ElhbQ5bp(8,8)
F#AakqsrlS(8,9)
F#q0W5IZKS(7,9)
F#MKPx2Wm0(7,10)
F#ykgIPne0(8,10)
F#ODPQAkeb(8,11)
F#SGaqmIKE(9,11)
F#2D7TDm5T(9,12)
F#SFD1yzVA(10,12)
F#bqm6gIkO(8,12)
F#F2SZX8qG(10,13)
F#4j5bIYrc(10,14)
F#85I4vXqL(10,15)
F#TXE8l2eg(11,15)
F#NK148IDP(11,14)
F#DF0FLtjJ(10,16)
F#bB8TY9vp(11,16)
F#J4EbXPew(10,17)
F#hbsu6vge(9,14)
F#zK5T9f5t(12,15)
F#bgovxgdK(13,15)
F#OC2sWMPw(13,16)
F#4Eg7E6PR(14,16)
F#nCUgAyT4(14,15)
F#Nrwxvfys(15,15)
F#uFPYdCpQ(15,16)
F#2icZ9PlC(16,16)
F#SJHQ4CuT(15,17)
F#L3J2qkFK(16,17)
F#b89xhCbU(16,18)
F#zLswxy3k(16,19)
F#6RJtTIEC(15,19)
F#ghvO2Huv(14,19)
F#QmgEagqc(13,19)
F#0cANVpvr(12,19)
F#XF0uEmPA(12,20)
F#HOZTtjkL(11,20)
F#AuCS6ocr(11,21)
F#dkDiWJOV(10,21)
F#PIISCJlF(9,21)
F#efxGHqie(9,20)
F#GnYKnQOX(9,19)
F#MIulo6pX(10,19)
F#txnLvjZO(11,19)
F#FlYuwVkr(12,18)
F#ET6WHHCG(11,18)
F#WOYlv9B6(10,18)
F#O4ta4rZs(9,18)
F#Q8vdsLXp(8,18)
F#xCxgoncs(8,19)
F#3s7TnPSj(8,20)
F#OgqKP1uQ(10,20)
F#e5uPn1WZ(11,22)
F#uytjvd0Z(11,23)
F#7R9jQ4tA(12,23)
F#ctuxLaIA(13,23)
F#KhSvbDtr(13,22)
F#FvqxzMeg(14,22)
F#HorAynFE(14,23)
F#QbuoELoZ(15,23)
F#0J9eWwk5(15,24)
F#DrjP4UAD(14,24)
F#Yd2897H9(15,25)
F#pAfGPaq7(15,26)
F#MlS3wXBW(15,27)
F#d38WtnA9(16,27)
F#kJLXyMWn(15,28)
F#96wdCABm(14,28)
F#Wtlwxhlp(13,28)
F#4qqRiXJZ(12,28)
F#ya70CBFi(11,28)
F#Gq9sspeN(10,28)
F#0TcysWCW(11,27)
F#YB4B8BCb(12,27)
F#WZhL3ecP(13,27)
F#VynNMBeO(14,27)
F#xSG62l3Q(13,29)
F#ql4U831v(14,29)
F#FaflyMEB(15,29)
F#bBcjNRsr(15,30)
F#U0rJQ1e1(15,31)
F#EqgVZWdL(16,31)
F#eQ1p7hyV(17,31)
F#NtMW0JWk(17,30)
F#RFxdfrJm(18,30)
F#h1oppjnK(19,30)
F#aarqW0er(18,29)
F#FZwDlzI4(19,29)
F#1Rib3pLo(20,29)
F#QLFNEuYV(20,28)
F#ycJgynUn(20,30)
F#GrhfMQqB(20,31)
F#HZtRZ8B2(21,31)
F#HhKhJEil(19,31)
F#JgeR7PAe(18,31)
F#p21aO7sF(17,29)
F#naqSn79c(20,32)
F#5t4RWKEl(20,33)
F#3pMELbU0(19,33)
F#UQDXIhoL(19,32)
F#X53tDBw2(18,32)
F#Dv3MPdvH(16,32)
F#wC8Vt0vC(17,32)
F#TohbCi4S(18,28)
F#UL7sgkio(16,29)
F#iq3YGRSm(16,28)
F#Dpbf3adD(17,28)
F#Ta52PHRA(17,27)
F#JVr0Bxyu(16,30)
F#oTWDAGRY(14,30)
F#SCEaATN4(14,31)
F#kxEf5W9o(13,30)
F#Y4G0hMBF(13,31)
F#ts1tEeOi(19,28)
F#7ZXs6QpU(21,28)
F#xnlIciqG(21,27)
F#T0VMaozF(21,26)
F#hHdc8Cn3(21,25)
F#BkFZBE2S(21,29)
F#lKBO5Mwx(21,30)
F#Sr0mxoZy(22,31)
F#WAx65Jb6(23,31)
F#HzGreOHM(23,32)
F#a8YNcwkp(24,31)
F#bGsYmpid(25,31)
F#5IjqcjWn(25,30)
F#oI9YXZe6(26,30)
F#K2Ef1eph(26,29)
F#rxeglUGI(26,31)
F#jJVOZG8P(26,32)
F#BJfiE7ik(25,32)
F#F4AtOQ9V(24,32)
F#zepvpwOS(25,33)
F#BaXoEDPo(25,34)
F#8DfoFxdt(25,35)
F#E8s04920(24,35)
F#QuHdGDoY(26,34)
F#2RUkSVQV(26,35)
F#aN1UA3E4(27,35)
F#5wwDY3gN(27,34)
F#GQ1aJD6W(27,33)
F#iivYN3KV(28,33)
F#g1cJHfES(28,34)
F#GJ2fT7yw(29,33)
F#WrKXEB7V(29,32)
F#m2xyWegD(30,33)
F#o6fFZIzF(31,33)
F#4GMXBtWQ(31,32)
F#FvO67m47(31,34)
F#afqHehzc(31,35)
F#bp7Ivz0D(32,33)
F#1FnVc6tj(32,34)
F#V8iTvgDi(30,35)
F#n3so5o9O(29,35)
F#OCmNrL53(30,34)
F#OVnnf7PV(32,35)
F#IKiC9jwf(33,34)
F#M6SbbW4O(34,34)
F#7xwyRG5u(34,35)
F#2MHF4uGd(35,34)
F#FlnIiCLV(35,35)
F#n358JVcz(35,36)
F#E6JbF4oP(35,37)
F#JdCVv1yF(34,37)
F#jKGXVNYI(34,36)
F#S2JXL1zT(33,37)
F#6zoXADft(33,36)
F#B44wQvyt(34,38)
F#LkXFGjfR(35,33)
F#BWr79mH3(36,34)
F#moMJMWUP(36,35)
F#E7SuvACx(34,33)
F#rfy2Ky90(36,33)
F#SbbhfcWZ(36,32)
F#Nk9OscTU(37,32)
F#WOVvWO1U(37,31)
F#D8QJ58Q2(38,31)
F#afUZv3Rm(38,32)
F#DMUcZUw8(36,31)
F#FnbtUoCp(36,30)
F#1dFs2fLZ(36,29)
F#L1Oyf5a4(36,28)
F#phT9Xaep(35,28)
F#KvCYhODM(35,29)
F#T7JCxFvU(37,29)
F#TeGZuthE(37,30)
F#6XxaDQR1(37,28)
F#dafMAnBm(36,27)
F#BKibMjwd(35,30)
F#OlSYkkn1(34,30)
F#ZTGAe26c(34,31)
F#BGye18Yl(34,32)
F#33gcfOPW(35,32)
F#6p0S3AQQ(35,31)
W#kcasa2V7(-1,-1)
W#sUlN8qoC(0,-1)
W#CoNtBs5b(1,-1)
W#DlMnZmOR(-1,0)
W#bEMtMreS(1,0)
W#xfjVXwLT(-1,1)
W#50ULgG9i(1,1)
W#1Ztg4uI3(0,0)
W#VY54rLho(-1,2)
W#HkTe0mr3(1,2)
W#vqT5IwYe(-1,3)
W#h8omRhLj(1,3)
W#nrKaV9lX(-1,4)
W#y3mKEemb(-1,5)
W#w2S34vmN(0,5)
W#KDu6DQP7(1,5)
W#SfSDJrhV(2,5)
W#AMHlJxPH(3,4)
W#WZG5a1Wp(3,5)
W#bU0fZDza(2,2)
W#bU3Hh4eI(3,2)
W#b203T8uf(4,2)
W#8nD3hSep(4,4)
W#DolENpqV(5,2)
W#Rq4qpDZx(6,2)
W#vHzR4fyK(6,3)
W#ewSgg5bP(4,5)
W#5sv1hCwk(5,5)
W#H9LFx7ZV(6,5)
W#APkvLTx3(7,3)
W#1sVMBNWX(7,5)
LP#EgVgUkSS(#1sVMBNWX)
bl#JJuSpi4b(#EgVgUkSS)<10>
W#FbiaSddz(8,3)
W#cY8gzOFW(9,3)
W#Xk4UP5i8(9,4)
W#sfppnHkt(10,4)
W#36W7Ec9v(10,5)
W#Y1aETFe2(10,6)
vn#IhsMXaHb(#Y1aETFe2)
W#caofER0r(10,7)
W#LnO5dvPt(9,8)
W#03qVCQIv(10,8)
W#hWpxQvnR(7,8)
W#KP2RRKMW(6,6)
W#HP2rex4T(6,7)
W#OxOgtPsT(6,8)
W#AddWCd7R(9,9)
W#KEFKimFj(9,10)
W#ZDsFMyDj(6,9)
W#N1n6qDgf(6,10)
W#IZjtX3Kk(6,11)
W#NH1iD9Cu(7,11)
W#qUTImhEG(7,12)
W#Jc6IONin(10,10)
W#xXju7SCS(10,11)
W#BxZQEq6f(8,13)
W#YuLtoT5a(9,13)
W#smvBqSun(11,11)
W#E8RlxhuP(11,12)
W#SRcEqrOH(11,13)
W#HRXKNjz6(7,13)
W#AVXQLEKy(9,15)
W#JItSRHkU(9,16)
W#x8o02tnY(12,14)
W#fNB0zGTb(12,16)
W#k5axwxAv(12,13)
ms#YyqJCWUj(#k5axwxAv)
W#TgVDCMhk(9,17)
W#OTSZU6Cr(11,17)
W#VrpN2sC6(12,17)
W#EId9np5O(8,14)
W#vftjt1oU(8,15)
ms#e7RlD7Sz(#vftjt1oU)
W#Bpxo6Txz(13,14)
W#VxyJg0kn(14,14)
W#vDxwCqLy(13,17)
W#YmgyzGQU(14,17)
W#zNmkgRAU(15,14)
W#4FhDOfZS(16,14)
W#awZVSARd(16,15)
W#AmPrQy9W(17,15)
W#Irdsc2ZD(17,16)
W#Qz3HpefN(17,17)
W#fh1tnLGl(14,18)
W#Ucv2151M(15,18)
W#hW0GprTD(17,18)
W#81WvwT12(17,19)
W#mnboI5se(15,20)
W#v6qhqfml(16,20)
W#LmmGQ0bP(17,20)
ms#A7BDhogX(#LmmGQ0bP)
W#Q1gYYigf(14,20)
W#fx66cJol(13,18)
W#1G2hcfic(13,20)
W#8E82sSM2(12,21)
W#OAhiaWpk(13,21)
W#o63JU8Pd(10,22)
W#qFuOvapC(12,22)
W#s8hGLaji(9,22)
W#8TqNtIXL(8,21)
W#dGGFpRkk(8,22)
vn#86upJ2uZ(#dGGFpRkk)
W#cfv7qY4f(8,17)
W#IRP4HI4X(7,17)
W#uGdI3WfZ(7,18)
W#BJMjdWab(7,19)
W#PHsc36Wf(7,20)
W#WrC6Om4K(7,21)
W#PtjcyPgU(10,23)
W#vr0F7B5j(10,24)
W#Hf3VgJP6(11,24)
W#Hhi8z2oK(12,24)
W#LzUM0ESB(13,24)
W#jInSMfJK(14,21)
W#RNwj0nPA(15,21)
W#3KAasXOW(15,22)
W#I8343CsJ(16,22)
W#jV5zHAgl(16,23)
W#7m0oMUcf(16,24)
W#g3TT4ozV(14,25)
W#cudtWZX2(16,25)
W#laC7LvT1(13,25)
W#bjvVWYka(14,26)
W#M9x5rVYu(16,26)
W#pV3MxU6d(17,26)
W#JaLRIZSX(12,29)
W#c47dNV8a(11,29)
W#UTtrpfIq(10,27)
W#lCstBlf8(10,29)
W#J5Klyp1Q(9,27)
vn#yt2vKZzg(#J5Klyp1Q)
W#gdcJ4k9V(9,28)
W#zZqH9E33(9,29)
vn#Tk79BE5H(#zZqH9E33)
W#0YBfDKkB(10,26)
W#iph2H2fu(11,26)
W#NAR3rX2a(12,26)
W#j0tqvqDO(13,26)
W#WQZv5CD4(12,30)
W#lmaEpoUK(14,32)
W#qGxy4u7s(15,32)
W#LvYYzmuJ(19,27)
W#d7OnOmdS(20,27)
W#gJNZiCO8(21,32)
W#kIsfZX6j(22,30)
W#ZUx0tG25(22,32)
W#D7OfqxgY(21,33)
W#VS2W6UIE(19,34)
W#TlfQrx7G(20,34)
W#cu2AwbGN(21,34)
ms#jW9JzzdN(#cu2AwbGN)
W#pS2tUkEv(18,33)
W#69KxjxMl(18,34)
W#PCpcyQDG(17,33)
W#7bP9B2T1(15,33)
W#qT7T9wJ2(16,33)
W#K53fD2oB(18,27)
W#NIIP9GDC(18,26)
W#aSUdyUu2(13,32)
W#SPuuYT0N(12,31)
W#rEiqUr2R(12,32)
W#ajlwxPwZ(22,27)
W#QiRyVYsv(22,28)
W#sZu6zVa9(22,29)
W#TTkTBdPq(20,26)
W#T8G6udCG(22,26)
W#bQ03byCP(20,25)
W#PT3Hmn90(22,25)
W#AIMVI2On(20,24)
W#Msqcvp5D(21,24)
W#NLCR3XIg(22,24)
W#nJwCk9Ur(23,30)
W#ohpUgXBm(24,30)
W#Dyfc94lT(22,33)
W#qScV7t2I(23,33)
W#x36100Iy(24,33)
W#dBSobHip(24,29)
W#p4XqITPp(25,29)
W#YV6Hwb9X(27,29)
W#IJ1zzYgY(27,30)
W#F4CpLU2G(27,31)
W#dS7uNbdZ(25,28)
W#nYYGMxg7(26,28)
W#pzH2wL59(27,28)
W#v2l8E42F(27,32)
W#9ebqKKao(26,33)
W#IWZc5ziR(24,34)
W#wjcixZN5(24,36)
W#WfduWOm3(25,36)
W#K087Ln3i(26,36)
W#C0pEEiS4(23,34)
W#3Ad5fUjI(23,35)
W#vStqlVLa(23,36)
LP#Mg6h1786(#vStqlVLa)
bl#xoNUW4Hu(#Mg6h1786)<10>
W#AvcugDkO(27,36)
W#VZj9NMez(28,35)
W#vkD4ntZp(28,36)
W#RxiYEbHE(28,32)
W#pYVvnhOk(29,34)
W#uuh4sBKk(30,32)
W#BnWjVwdd(28,31)
vn#ptqnPlq2(#BnWjVwdd)
W#rnFcE6wi(29,31)
W#KwT9Vad4(30,31)
W#BPGeQLWJ(32,32)
W#J6vUwnDT(31,31)
W#aKCGijx4(32,31)
W#Y7ZCbeNA(30,36)
W#08AhZjMV(31,36)
W#B2vvNOeo(32,36)
W#Ds63cdbh(33,32)
W#9Kt8lEKB(33,33)
W#60EiNfhn(33,35)
W#L79EsM2x(29,36)
ms#nP8UyP65(#L79EsM2x)
W#azxlE0NI(36,36)
W#oF3Daog1(36,37)
W#XWHHUCUZ(35,38)
W#EyCBsPBy(36,38)
W#nFKS6Xjb(33,38)
W#goWmIQUL(32,37)
W#e8bG18ul(32,38)
W#4Mn8S3jB(33,39)
W#HE6JmqNU(34,39)
W#0bcVLG9O(35,39)
W#mJEPRjVA(37,33)
W#3kiVRnO1(37,34)
W#hViLXtvC(37,35)
W#KMGRFVRy(37,36)
W#0UjgNwYe(38,33)
W#bnAjJy5m(38,30)
W#IhY7UN84(39,30)
W#psO1W5Mn(39,31)
W#nhUUbSuh(39,32)
W#FGY0ueBb(39,33)
W#V8MreBlR(35,27)
W#hxVD5BMo(37,27)
W#hVRjfrBv(34,27)
W#6BPpHaJo(34,28)
W#DF7eQYWj(34,29)
ms#0moaMqGg(#DF7eQYWj)
W#vPrFps9t(38,28)
W#f1xF3kH2(38,29)
W#65TsKKXW(38,27)
W#LyPqOJz4(35,26)
W#vSaxMkPP(36,26)
W#QEw2NdFk(37,26)
W#3ih1ffR8(33,29)
W#TmcZ4Zmo(33,30)
W#cgzX2nan(33,31)
C#yRdHPqnu(#TXE8l2eg)
K#3g9RobP2(#yRdHPqnu)
D#6TmNQYmd(#HE6JmqNU)
bl#5l8bSEvr(#6TmNQYmd)<15>
x#PInH48Jr(#6TmNQYmd)<It's locked!>
D#7waq5HvI(#vPrFps9t)
bl#BrL4pF3u(#7waq5HvI)<15>
x#I3kCEG0T(#7waq5HvI)<It's locked!>
@#uZXxEkqK(#L1Oyf5a4)
wa#moSXPzwO(#uZXxEkqK)
bl#rrQp9cpa(#uZXxEkqK)<197>
n#QB2pREwG(#uZXxEkqK)<Dr Halyn>
WP#21i30ZRG(#uZXxEkqK)<Stick>
ds#bKHsJDcK(#21i30ZRG)<It's super ineffective.>
bl#k87FL1ln(#L1Oyf5a4)<1>
bl#vhlBxgD5(#dafMAnBm)<1>
```