<?php


namespace AppleSignIn;


use UnexpectedValueException;

class JWT extends \Firebase\JWT\JWT
{
    /**
     * @param string $jwt
     * @return mixed
     */
    public static function getPublicKeyKid($jwt)
    {
        $tks = explode('.', $jwt);
        if (count($tks) != 3) {
            throw new UnexpectedValueException('Wrong number of segments');
        }
        list($headb64, $bodyb64, $cryptob64) = $tks;
        if (null === ($header = static::jsonDecode(static::urlsafeB64Decode($headb64)))) {
            throw new UnexpectedValueException('Invalid header encoding');
        }
        return $header->kid;
    }
}
