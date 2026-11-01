# agar-clone

## Contents
 - [Introduction](#introduction)
 - [Setup](#setup)
 - [Test and Known Issues](#test-and-known-issues)
 - [FAQ](#faq)
 - [Protocol Extensions](#protocol-extensions)
 - [Contributions](#contributions)
 - [Credits](#credits)
 - [Disclaimer](#disclaimer)

## Introduction

An agar.io-style browser client for compatible game servers. The interface uses
the neutral name **Agar Clone**; project history and acknowledgements are listed
under [Credits](#credits).

Features include:

- [x] Hack main_out.js and make the variables and function names readable. Now most of the variable and function name should make sense. 
- [x] Add uploading custom skins features (Done)
- [x] Add [in-game chat](https://cloud.githubusercontent.com/assets/5158896/8327532/c41e94fa-1a9b-11e5-87cc-f42b5f6ef2df.png) feature. (Done)
- [x] Add mobile device support (Done)

## Setup

Copy all files to a server with PHP support (e.g. LEMP/LAMP). To check whether your server supports PHP, please read [this tutorial](http://www.cyberciti.biz/faq/how-do-i-test-php-installation-with-a-phpinfo-page/).
Once everything is set up, open index.html in your browser. If everything is working well, go to the next section to use your own Ogar server.

The skins gallery loads PNGs directly, newest first.

### Support Your Server
Set each game mode's option value in `index.html` to its WebSocket server; Addresses can be `HOST:PORT`, `ws://HOST:PORT`, or `wss://HOST:PORT`. Without a scheme, the client uses `wss://` on HTTPS pages and `ws://` otherwise; browsers block `ws://` connections from HTTPS pages.

### Upload Custom Skins
The upload module does two things. The first is to upload a skin onto skins folder. The second is that the client request the server to run checkdir.php every 15 seconds to check what skins are uploaded and add them into knownNameDict. Then the skins can be loaded. 

Open `uploads.html` to upload a PNG (up to 2 MiB and 4096 × 4096 pixels).
Use 1–13 letters, numbers or hyphens in the filename (excluding `.png`), e.g. `earth.png`, then put
`{earth}` in your in-game nickname. Names are saved in lowercase; existing skins
cannot be overwritten. Keep the full nickname within the game's 15-character limit.

The PHP handler is `uploads.php`. Give the web server user write access to the
`skins/` folder using appropriate ownership or group permissions; do not make it
world-writable. PHP's `upload_max_filesize` must be at least `2M`, and
`post_max_size` should be larger (e.g. `3M`) to allow for the multipart form data.



## Test and Known Issues

The original documentation reports testing on a LEMP stack with OS X 10.10 and
Ubuntu 14.04 using [Ogar](https://github.com/vram4/Ogar). This is historical
information, not a statement of current platform support.

Some variable and function names still need clarification.

## FAQ
### Can agar-clone connect to the official server?
No, agar-clone is not intended to be used with the official server. You will have to use it with [Ogar](https://github.com/forairan/Ogar) or [Cigar](https://github.com/m-byte/Cigar).

### How can I use the chat feature?
Chat requires a server supporting this client's chat protocol, such as
[Cigar](https://github.com/m-byte/Cigar). Other servers may require protocol changes.

## Protocol extensions
As the server has to tell whether the client supports protocol extensions, the values of package 254 and 255 have been adjusted.
Package 255 now contains "Ogar" in ASCII (1332175218) while package 254 contains the version number. When modifying the protocol,
only new features can be implemented. Also, the client must remain compatible with older versions of the server.

Original protocol can be found [here](https://github.com/vram4/Agar.io-Protocol). Changes to the protocol have to be [documented](PROTOCOL.md).

## Contributions

Pull Request are welcome. 

## Credits

- **Tomsterley** — credited for the earlier client this version builds on;
  see [RowanHarley/agario](https://github.com/RowanHarley/agario) for the earlier upstream repository.
- **Other contributors** — see the Git history for individual contributions.

These acknowledgements preserve the project's history; they do not imply that
any credited author currently maintains this version or operates its game servers.
This is an independent client, not the official agar.io service.

## Disclaimer

Note that the aim of this repo is to study agar.io client, understand the mechanism and find potential improvements. It is worth pointing out that hosting a third-party agar client is against the [Terms](http://agar.io/terms.txt) of Agario and not recommended by the owner of this repo. Please think twice before hosting the client and do it at your own risk. :)
