<?php

namespace AppleSignIn;

use UnexpectedValueException;

/**
 * Class JWT
 * @todo(pmuir): break the inheritance here, it's not needed and we should prefer composition, wrapping the Firebase
 * static methods directly instead of what I have done here.
 *
 * @package AppleSignIn
 */
class JWT extends \Firebase\JWT\JWT
{
    /**
     * @param string $jwt
     * @return mixed
     */
    public function getPublicKeyKid($jwt)
    {
        $tks = explode('.', $jwt);
        if (count($tks) !== 3) {
            throw new UnexpectedValueException('Wrong number of segments');
        }
        [$headb64, $bodyb64, $cryptob64] = $tks;
        if (null === ($header = static::jsonDecode(static::urlsafeB64Decode($headb64)))) {
            throw new UnexpectedValueException('Invalid header encoding');
        }
        return $header->kid;
    }

    /**
     * @inheritDoc
     */
    public function decodeJwt($jwt, $key, array $allowed_algs = array())
    {
        return self::decode($jwt, $key, $allowed_algs);
    }
}
