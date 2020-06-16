<?php

namespace AppleSignIn;

use Exception;

/**
 * Decode Sign In with Apple identity token, and produce an ASPayload for
 * utilizing in backend auth flows to verify validity of provided user creds.
 *
 * @package  AppleSignIn
 * @author   Griffin Ledingham <gcledingham@gmail.com>
 * @license  http://opensource.org/licenses/BSD-3-Clause 3-clause BSD
 * @link     https://github.com/GriffinLedingham/php-apple-signin
 */
class Decoder
{
    /**
     * @var PublicKeyFetcher
     */
    private $keyFetcher;
    /**
     * @var JWT
     */
    private $JWT;

    /**
     * Decoder constructor.
     * @param JWT $JWT
     * @param PublicKeyFetcher $keyFetcher
     */
    public function __construct(JWT $JWT, PublicKeyFetcher $keyFetcher)
    {
        $this->keyFetcher = $keyFetcher;
        $this->JWT = $JWT;
    }

    /**
     * Parse a provided Sign In with Apple identity token.
     *
     * @param string $identityToken
     * @return Payload
     * @throws \AppleSignIn\Exception
     */
    public function getAppleSignInPayload(string $identityToken): Payload
    {
        $identityPayload = $this->decodeIdentityToken($identityToken);
        return new Payload($identityPayload);
    }

    /**
     * Decode the Apple encoded JWT using Apple's public key for the signing.
     *
     * @param string $identityToken
     * @return object
     * @throws Exception
     */
    private function decodeIdentityToken(string $identityToken): object
    {
        $publicKeyKid = $this->JWT->getPublicKeyKid($identityToken);

        $publicKeyData = $this->fetchPublicKey($publicKeyKid);

        $publicKey = $publicKeyData['publicKey'];
        $alg = $publicKeyData['alg'];

        return $this->JWT->decodeJwt($identityToken, $publicKey, [$alg]);
    }

    /**
     * Fetch Apple's public key from the auth/keys REST API to use to decode
     * the Sign In JWT.
     *
     * @param string $publicKeyKid
     * @return array
     * @throws Exception
     */
    private function fetchPublicKey(string $publicKeyKid): array
    {
        return $this->keyFetcher->fetch($publicKeyKid);
    }
}
