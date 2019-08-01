<?php

namespace AppleSignIn;

use AppleSignIn\Vendor\JWK;
use AppleSignIn\Vendor\JWT;

use Exception;

class ASDecoder {
    public static function getAppleSignInPayload(string $identityToken) : ?object
    {
        $identityPayload = self::decodeIdentityToken($identityToken);
        return new ASPayload($identityPayload);
    }

    public static function decodeIdentityToken(string $identityToken) : object {
        $publicKeyData = self::fetchPublicKey();

        $publicKey = $publicKeyData['publicKey'];
        $alg = $publicKeyData['alg'];

        $payload = JWT::decode($identityToken, $publicKey, [$alg]);

        return $payload;
    }

    public static function fetchPublicKey() : array {
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

class ASPayload {
    protected $_instance;

    public function __construct(?object $instance) {
        if(is_null($instance)) {
            throw new Exception('ASPayload received null instance.');
        }
        $this->_instance = $instance;
    }

    public function __call($method, $args) {
        return call_user_func_array(array($this->_instance, $method), $args);
    }

    public function __get($key) {
        return $this->_instance->$key;
    }

    public function __set($key, $val) {
        return $this->_instance->$key = $val;
    }

    public function getEmail() : ?string {
        return (isset($this->_instance->email)) ? $this->_instance->email : null;
    }

    public function getUser() : ?string {
        return (isset($this->_instance->sub)) ? $this->_instance->sub : null;
    }

    public function verifyUser(string $user) : bool {
        return $user === $this->getUser();
    }
}