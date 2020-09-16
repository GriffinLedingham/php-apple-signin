<?php

include '../vendor/autoload.php';

use AppleSignIn\ASDecoder;

$clientUser = "example_client_user";
$identityToken = "example_encoded_jwt";

// cache get
$cacheKeys = [];

ASDecoder::$publicKeys = $cacheKeys;

$appleSignInPayload = ASDecoder::getAppleSignInPayload($identityToken);

// cache save
$cacheKeys = ASDecoder::$publicKeys;

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
//$a = \AppleSignIn\ASCurl::get("https://appleid.apple.com/auth/keys", null, $errors);
//var_dump($a);
