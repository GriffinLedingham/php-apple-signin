<?php

namespace AppleSignInTest\Unit;

use AppleSignIn\JWT;
use PHPUnit\Framework\TestCase;

class JWTTest extends TestCase
{

    public function testGetPublicKeyKid()
    {
        $claims = [
            "alg" => "ES256",
            "kid" => "TESTKID",
            "typ" => "JWT"
        ];
        $token = JWT::urlsafeB64Encode(JWT::jsonEncode($claims)) . '.xxx.xxx';

        $result = (new JWT())->getPublicKeyKid($token);

        $this->assertEquals('TESTKID', $result);
    }
}
