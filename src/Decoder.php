<?php

namespace AppleSignIn;

use Exception;

/**
 * Decode Sign In with Apple identity token, and produce an ASPayload for
 * utilizing in backend auth flows to verify validity of provided user creds.
 *
 * @package  AppleSignIn\ASDecoder
 * @author   Griffin Ledingham <gcledingham@gmail.com>
 * @license  http://opensource.org/licenses/BSD-3-Clause 3-clause BSD
 * @link     https://github.com/GriffinLedingham/php-apple-signin
 */
class Decoder
{
    /**
     * Parse a provided Sign In with Apple identity token.
     *
     * @param string $identityToken
     * @return object|null
     * @throws Exception
     */
    public static function getAppleSignInPayload(string $identityToken): ?object
    {
        $identityPayload = self::decodeIdentityToken($identityToken);
        return new Payload($identityPayload);
    }

    /**
     * Decode the Apple encoded JWT using Apple's public key for the signing.
     *
     * @param string $identityToken
     * @return object
     * @throws Exception
     */
    public static function decodeIdentityToken(string $identityToken): object
    {
        $publicKeyKid = JWT::getPublicKeyKid($identityToken);

        $publicKeyData = self::fetchPublicKey($publicKeyKid);

        $publicKey = $publicKeyData['publicKey'];
        $alg = $publicKeyData['alg'];

        return JWT::decode($identityToken, $publicKey, [$alg]);
    }

    /**
     * Fetch Apple's public key from the auth/keys REST API to use to decode
     * the Sign In JWT.
     *
     * @param string $publicKeyKid
     * @return array
     * @throws Exception
     */
    public static function fetchPublicKey(string $publicKeyKid): array
    {
        return (new PublicKeyFetcher(new Http\Curl()))->fetch($publicKeyKid);
    }
}
