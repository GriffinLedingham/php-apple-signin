<?php

namespace AppleSignIn;

use AppleSignIn\Http\Client;

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
        $decodedPublicKeys = json_decode($publicKeys, true);

        if (!isset($decodedPublicKeys['keys']) || count($decodedPublicKeys['keys']) < 1) {
            throw new Exception('Invalid key format.');
        }

        $kids = array_column($decodedPublicKeys['keys'], 'kid');
        $parsedKeyData = $decodedPublicKeys['keys'][array_search($publicKeyId, $kids, false)];
        $parsedPublicKey = JWK::parseKey($parsedKeyData);
        $publicKeyDetails = openssl_pkey_get_details($parsedPublicKey);

        if (!isset($publicKeyDetails['key'])) {
            throw new Exception('Invalid public key details.');
        }

        return [
            'publicKey' => $publicKeyDetails['key'],
            'alg' => $parsedKeyData['alg']
        ];
    }
}