# Bullhorn Provider for OAuth 2.0 Client

This package provides [Bullhorn](https://www.bullhorn.com/) OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

To install, use composer:

```
composer require jiminny/oauth2-bullhorn
```

## Usage

For usage and code examples, check out the PHP League's [basic usage guide](https://oauth2-client.thephpleague.com/usage/).


## Caveats

Bullhorn does not actually use OAuth across their API. One needs to perform a REST API Login call to obtain what they call `bhrestToken` and use that intead.

Few notes:
* As is expected the OAuth refresh token will be invalidated on first usage
* A bit surprising the access token is being invalidated on it's first usage as well
  * We've observed this on non-successful requests as well (500 response) 
  * The token is served with expiration time 600, it's recommended to manually expire it on a successfull call.