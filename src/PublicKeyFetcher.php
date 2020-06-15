<?php

namespace AppleSignIn;

use AppleSignIn\Http\Client;
use Firebase\JWT\JWK;

/**
 * Class PublicKeyFetcher is responsible for using a Http\Client to retrieve Apple's public keys
 *
 * @package AppleSignIn
 */
class PublicKeyFetcher
{
    private const PUBLIC_KEY_LIST_URL = 'https://appleid.apple.com/auth/keys';

    /** @var Client */
    private $httpClient;

    /**
     * PublicKeyFetcher constructor.
     * @param Client $httpClient
     */
    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $publicKeyId
     * @return array
     * @throws Exception
     */
    public function fetch(string $publicKeyId): array
    {
        $publicKeys = $this->httpClient->get(self::PUBLIC_KEY_LIST_URL);
        if (!is_string($publicKeys)) {
            throw new Exception('Invalid response from Apple.');
        }

        try {
            $publicKeyDetails = JWK::parseKeySet(json_decode($publicKeys, true));
        } catch (\Exception $e) {
            throw new Exception('Could not parse key response from Apple.', 0, $e);
        }

        if (!isset($publicKeyDetails[$publicKeyId])) {
            throw new Exception('Invalid public key details.');
        }

        return [
            'publicKey' => $publicKeyDetails[$publicKeyId],
            'alg'       => 'RS256',
        ];
    }
}
