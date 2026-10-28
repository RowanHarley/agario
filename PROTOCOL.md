# PROTOCOL

## Protocol extensions
The client negotiates [Agar.io protocol 4](https://github.com/Cigar2/Agar.io-Protocol/blob/master/protocol_4-8.md)
with packet 254 (uint32 little-endian value 4). Packet 255 retains the "Ogar" extension marker
(1332175218) as its handshake key for servers supporting the extended chat protocol.

## Changes
Please document any changes introduced here. Direction has to be either `S2C` (server to client) or `C2S`.

### Version 1


| Direction  | ID  | Value | Description|
| :------------ |:---------------| :-----|:----|
|C2S       | 254 |                4 | Agar.io wire protocol version|
|C2S       | 255 |       1332175218 | Legacy Ogar extension handshake key (uint32 little-endian)|
|S2C       |  48 | behaves like #49 | Leaderboard without numbers|
|C2S       |  99 | flags (uint8) [1], message | For in-game chat, each char is in Uint16 |
|S2C       |  99 | flags (uint8) [1], r (uint8), g (uint8), b (uint8), nick, '\0', message, '\0' | same as above |

 [1] If flags has the 2, 4, or 8 bit set, an offset of 4, 8, or 16 bytes follows before any other data. Until these additional
   bytes are used, just skip them. 

Hint : to use Packet 48, make sure the packet ID in case 48 of Ogar/src/packet/UpdateLeaderboard.js is adjusted like this: 
```
view.setUint8(0, this.packetLB, true);
```
You can also use https://github.com/m-byte/Ogar, which already has this feature enabled.

### Version 2
TODO: We could add chat functionality in this version. That way, only one websocket server would be needed.
