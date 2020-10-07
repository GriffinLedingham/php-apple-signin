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

/**
 * Client user will be input by frontend during an API call, is named as "userIdentifier" from frontend SDK result
 */
$clientUser = "001908.a2ebc8408adb43dfb2a7b32c7fd50899.10103";

/**
 * Identity token will be input by frontend during an API call, is named as "identityToken" from frontend SDK result
 */
$identityToken = 'eyJraWQiOiJBSURPUEsxIiwiYWxnIjoiUlMyNTYifQ.eyJpc3MiOiJodHRwczovL2FwcGxlaWQuYXBwbGUuY29tIiwiYXVkIjoiY29tLmt1aW5pdS5rbiIsImV4cCI6MTU3OTUwODAzOSwiaWF0IjoxNTc5NTA3NDM5LCJzdWIiOiIwMDE5MDguYTJlYmM4NDA4YWRiNDNkZmIyYTdiMzJjN2ZkNTA4OTkuMTAxMCIsImNfaGFzaCI6ImxpbDg0ZEw0SHpjVE9VSkt4SHlXdUEiLCJlbWFpbCI6IjE1NTY5NjMwNzBAcXEuY29tIiwiZW1haWxfdmVyaWZpZWQiOiJ0cnVlIiwiYXV0aF90aW1lIjoxNTc5NTA3NDM5fQ.bTFCjMAT3dr6fARIlxg4W0zLgpBDds0LWSZnnPHGMe-YDN8sEZH-aNHjidJTO7GbmNtscGLhsRYM-qGv9j8XGFEYKh2UW23eLiS54CbPgNwobFmlWeSOeGm8qIcOXJ2P0xhy5untWQ2WBJXia4JpneDdDWksEQIBVtV_tC2zHPXlP5KUqMoEIyylLEZ87DPSRCa0AAOiqqbV9ookHrlMzEgYMGuurjVUm3p_0NgbOzauli9hy-j2-_0UwYQnaxY8ABGwcIBBiTgv_bUlnnqIDnE5D4nNIg0356B0-xgvjFrGPyOKh0H7hQQWpjHvbRxk1vnnNRjZ6CkLiUiPlp7Cog';

try {
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
}
catch(Exception $e) {
    //Login failed
}

if($isValid) {
    //Login success
}
else {
    //Login failed
}
?>
```
