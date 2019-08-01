<?php

namespace AppleSignIn;
use Firebase\JWT;
use Firebase\JWK;
use Exception;

class ASDecoder {
    public static function getAppleSignInPayload(string $identityToken) : array
    {
        return self::decodeIdentityToken($identityToken);
    }

    public static function getAppleSignInUser(string $identityToken) : string
    {
        $identityPayload = self::getAppleSignInPayload($identityToken);
        return (isset($identityPayload['sub'])) ? $identityPayload['sub'] : null;
    }

    public static function getAppleSignInEmail(string $identityToken) : string
    {
        $identityPayload = self::getAppleSignInPayload($identityToken);
        return (isset($identityPayload['email'])) ? $identityPayload['email'] : null;
    }

    public static function verifyAppleSignInUser(string $user, string $identityToken) : bool
    {
        $identityPayload = self::getAppleSignInPayload($identityToken);
        return ($identityPayload->sub === $user);
    }

    public static function decodeIdentityToken(string $identityToken) {
        [$publicKey, $alg] = self::fetchPublicKey();
        $payload = JWT::decode($identityToken, $publicKey, [$alg]);
        return $payload;
    }

    public static function fetchPublicKey() {
        $publicKeys = file_get_contents('https://appleid.apple.com/auth/keys');
        $decodedPublicKeys = json_decode($publicKeys, true);

        if(!isset($decodedPublicKeys['keys']) || count($decodedPublicKeys['keys']) < 1) {
            throw new Exception('Invalid key format.');
        }

        $parsedKeyData = $decodedPublicKeys['keys'][0];
        $parsedPublicKey= JWK::parseKey($parsedKeyData);
        $publicKeyDetails = openssl_pkey_get_details($parsedPublicKey);

        if(!isset($publicKeyDetails['key'])) {
            throw new Exception('Invalid public key details.');
        }

        return [
            'publicKey' => $publicKeyDetails['key'],
            'alg' => $parsedKeyData['alg']
        ];
    }
}