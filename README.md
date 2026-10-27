# agar-clone

## Contents
 - [Introduction](#introduction)
 - [Setup](#setup)
 - [Test and Known Issues](#test-and-known-issues)
 - [FAQ](#faq)
 - [Protocol Extensions](#protocol-extensions)
 - [Contributions](#contributions)
 - [Disclaimer](#disclaimer)

## Introduction

This is a clone of agar.io. In this project we aim to acheive the following. 

- [x] Hack main_out.js and make the variables and function names readable. Now most of the variable and function name should make sense. 
- [x] Add uploading custom skins features (Done)
- [x] Add [in-game chat](https://cloud.githubusercontent.com/assets/5158896/8327532/c41e94fa-1a9b-11e5-87cc-f42b5f6ef2df.png) feature. (Done)
- [x] Add mobile device support (Done)
- [x] Support multi-server feature (Done in Cigar)

##Setup

Copy all files to a server with PHP support (e.g. LEMP/LAMP). To check whether your server supports PHP, please read [this tutorial](http://www.cyberciti.biz/faq/how-do-i-test-php-installation-with-a-phpinfo-page/).
Once everything is set up, open index.html in your browser. If everything is working well, go to the next section to use your own Ogar server.

The skins gallery (`gallery/img.php`) requires the PHP GD extension. Install and enable `php-gd` for the PHP version used by your web server, then restart the web server. You can check whether GD is enabled with `php -m` (look for `gd`).

### Support Your Server
Set each game mode's option value in `index.html` to its WebSocket server; Addresses can be `HOST:PORT`, `ws://HOST:PORT`, or `wss://HOST:PORT`. Without a scheme, the client uses `wss://` on HTTPS pages and `ws://` otherwise; browsers block `ws://` connections from HTTPS pages.

### Upload Custom Skins
The upload module does two things. The first is to upload a skin onto skins folder. The second is that the client request the server to run checkdir.php every 15 seconds to check what skins are uploaded and add them into knownNameDict. Then the skins can be loaded. 

To make this feature work, you need to give upload.php priviliage to read skins folder. 


``` 
chmod 777 skins 
```

will make it work.



## Test and Known Issues

This client has been tested on LEMP stack on OS X 10.10 and Ubuntu 14.04 with server side [Ogar](https://github.com/vram4/Ogar).

It is known that some of the variables and function names still make no sense, we are working on that. 

## FAQ
### Can agar-clone connect to the official server?
No, agar-clone is not intended to be used with the official server. You will have to use it with [Ogar](https://github.com/forairan/Ogar) or [Cigar](https://github.com/m-byte/Cigar).

### How can I use the chat feature?
Currently, only [Cigar](https://github.com/m-byte/Cigar) supports the chat protocol. If you know of another server supporting the extended protocol, feel free to [let us know](https://github.com/Eureka22/agar-clone/issues).

## Protocol extensions
As the server has to tell whether the client supports protocol extensions, the values of package 254 and 255 have been adjusted.
Package 255 now contains "Ogar" in ASCII (1332175218) while package 254 contains the version number. When modifying the protocol,
only new features can be implemented. Also, the client must remain compatible with older versions of the server.

Original protocol can be found [here](https://github.com/vram4/Agar.io-Protocol). Changes to the protocol have to be [documented](PROTOCOL.md).

## Contributions

Pull Request are welcome. 

## Disclaimer

Note that the aim of this repo is to study agar.io client, understand the mechanism and find potential improvements. It is worth pointing out that hosting a third-party agar client is against the [Terms](http://agar.io/terms.txt) of Agario and not recommended by the owner of this repo. Please think twice before hosting the client and do it at your own risk. :)
