<?php

namespace AppleSignInTest\Unit;

use AppleSignIn\Payload;
use PHPUnit\Framework\TestCase;

/**
 * Class PayloadTest
 * @package AppleSignInTest\Unit
 */
class PayloadTest extends TestCase
{

    public function testGetUserUUIDReturnsSubClaim()
    {
        $jwtPayload = new \stdClass();
        $jwtPayload->sub = 'some-user-uuid-string';
        $payload = new Payload($jwtPayload);

        $this->assertEquals('some-user-uuid-string', $payload->getUserUUID());
    }
}
