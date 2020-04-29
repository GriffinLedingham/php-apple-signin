<?php

namespace AppleSignIn;

use AppleSignIn\Vendor\JWK;
use AppleSignIn\Vendor\JWT;

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
class ASDecoder {
    
    /* The cache file name for the Apple public keys
     *
     */
    public static $savedFileName = __DIR__. '/apple.keys';
    
    /* Set a maximum time in seconds to cache the Apple keys.  The official recommendation
     * is 0 seconds, but in production, that seems to be overkill.  The download function
     * built here falls back to fetching a new copy if the key is not found.  This would
     * obviously not capture Apple suddenly revoking a public key, but that would probably
     * not occur without notification.
     */
    public static $cacheTime = 3600;
    
    /**
     * Parse a provided Sign In with Apple identity token.
     *
     * @param string $identityToken
     * @return object|null
     */
    public static function getAppleSignInPayload(string $identityToken) : ?object
    {
        $identityPayload = self::decodeIdentityToken($identityToken);
        return new ASPayload($identityPayload);
    }

    /**
     * Decode the Apple encoded JWT using Apple's public key for the signing.
     *
     * @param string $identityToken
     * @return object
     */
    public static function decodeIdentityToken(string $identityToken) : object {
        $publicKeyKid = JWT::getPublicKeyKid($identityToken);

        $publicKeyData = self::fetchPublicKey($publicKeyKid);

        $publicKey = $publicKeyData['publicKey'];
        $alg = $publicKeyData['alg'];

        JWT::$leeway = 60;
        $payload = JWT::decode($identityToken, $publicKey, [$alg]);

        return $payload;
    }

    
    protected static function downloadPublicKey(){
        $fileUrl = 'https://appleid.apple.com/auth/keys';
        
        $ch = curl_init($fileUrl);
        $fp = fopen (self::$savedFileName, 'w+');
        
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
        curl_close($ch);
        
        fclose($fp);
        
        $json = file_get_contents(self::$savedFileName);
        
        return json_decode($json, TRUE);
    }
    
    /* A recursive function that looks for an existing public key in a file
     * and if it is not found, or that file has expired, downloads a new
     * copy of the Apple keys.
     */
    
    public static function fetchPublicKey(string $publicKeyKid, $newDownload = FALSE) : array {
        
        $decodedPublicKeys = array();
        
        if (!file_exists(self::$savedFileName)||$newDownload){
            $decodedPublicKeys = self::downloadPublicKey();
            $newDownload = TRUE;
        }
        else{
            if (time()-filemtime(self::$savedFileName)>self::$cacheTime){
                $decodedPublicKeys = self::downloadPublicKey();
                $newDownload = TRUE;
            }
            else{
                $json = file_get_contents(self::$savedFileName);
                
                $decodedPublicKeys = json_decode($json, TRUE);
            }
        }
        
        if(!isset($decodedPublicKeys['keys']) || count($decodedPublicKeys['keys']) < 1) {
            if ($newDownload == TRUE){
                throw new Exception('Invalid key format.');
            }
            else{
                self::fetchPublicKey($publicKeyKid,TRUE);
            }
        }
        
        $kids = array_column($decodedPublicKeys['keys'], 'kid');
        $parsedKeyData = $decodedPublicKeys['keys'][array_search($publicKeyKid, $kids)];
        $parsedPublicKey= JWK::parseKey($parsedKeyData);
        $publicKeyDetails = openssl_pkey_get_details($parsedPublicKey);
        
        if(!isset($publicKeyDetails['key'])) {
            if ($newDownload == TRUE){
                throw new Exception('Invalid public key details.');
            }
            else{
                self::fetchPublicKey($publicKeyKid,TRUE);
            }
        }
        
        return  [
            'publicKey' => $publicKeyDetails['key'],
            'alg' => $parsedKeyData['alg']
        ];
    }
    
}

/**
 * A class decorator for the Sign In with Apple payload produced by
 * decoding the signed JWT from a client.
 */
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
        return (isset($this->_instance->$key)) ? $this->_instance->$key : null;
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
