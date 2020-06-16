<?php

include '../vendor/autoload.php';

use AppleSignIn\Decoder;
use AppleSignIn\Http\Curl;
use AppleSignIn\JWT;
use AppleSignIn\PublicKeyFetcher;

$clientUser = "example_client_user";
$identityToken = "example_encoded_jwt";

$decoder = new Decoder(
    new JWT(),
    new PublicKeyFetcher(new Curl())
);
$appleSignInPayload = $decoder->getAppleSignInPayload($identityToken);

/**
 * Obtain the Sign In with Apple email and user creds.
 */
$email = $appleSignInPayload->getEmail();
$user = $appleSignInPayload->getUser();

$AppleUserUUID = $appleSignInPayload->getUserUUID();

/**
 * Determine whether the client-provided user is valid.
 */
$isValid = $appleSignInPayload->verifyUser($clientUser);



//test download apple auth keys
$fetcher = new PublicKeyFetcher(new Curl());

try {
    $a = $fetcher->fetch('86D88Kf');
} catch (\AppleSignIn\Exception $e) {
    var_dump($e);
}

var_dump($a);
