<?php

include '../vendor/autoload.php';

use AppleSignIn\Decoder;
use AppleSignIn\Http\Curl;
use AppleSignIn\PublicKeyFetcher;

$clientUser = "example_client_user";
$identityToken = "example_encoded_jwt";

$appleSignInPayload = Decoder::getAppleSignInPayload($identityToken);

/**
 * Obtain the Sign In with Apple email and user creds.
 */
$email = $appleSignInPayload->getEmail();
$user = $appleSignInPayload->getUser();

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
