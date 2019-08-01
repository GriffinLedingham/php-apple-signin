php-apple-signin
=======
PHP library to manage Sign In with Apple identifier tokens, and validate them server side passed through by the iOS client.

Installation
------------

Use composer to manage your dependencies and download php-apple-signin:

```bash
composer require griffinledingham/php-apple-signin
```

Example
-------
```php
<?php
use AppleSignIn\ASDecoder;

$clientUser = "example_client_user";
$identityToken = "example_encoded_jwt";

$appleSignInPayload = ASDecoder::getAppleSignInPayload($identityToken);

/**
 * Obtain the Sign In with Apple email and user creds.
 */
$email = $appleSignInPayload->getEmail();
$user = $appleSignInPayload->getUser();

/**
 * Determine whether the client-provided user is valid.
 */
$isValid = $appleSignInPayload->verifyUser($clientUser);

?>
```
