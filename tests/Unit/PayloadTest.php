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
    /** @var \stdClass */
    private $jwtPayload;

    public function setUp()
    {
        parent::setUp();

        $this->jwtPayload = new \stdClass();
        $this->jwtPayload->sub = 'some-user-uuid-string';
        $this->jwtPayload->email = 'user@example.com';
    }

    /**
     * @expectedException \AppleSignIn\Exception
     * @expectedExceptionMessage Payload received null JWT.
     */
    public function testPayloadInstanciatedWithNullObject()
    {
        $payload = new Payload(null);
    }

    /**
     * @expectedException \AppleSignIn\Exception
     * @expectedExceptionMessage Payload received invalid JWT. Missing email claim.
     */
    public function testPayloadInstanciatedWithMissingEmailClaim()
    {
        unset($this->jwtPayload->email);

        $payload = new Payload($this->jwtPayload);
    }

    /**
     * @expectedException \AppleSignIn\Exception
     * @expectedExceptionMessage Payload received invalid JWT. Missing subject claim.
     */
    public function testPayloadInstanciatedWithMissingSubClaim()
    {
        unset($this->jwtPayload->sub);

        $payload = new Payload($this->jwtPayload);
    }

    public function testGetUserUUIDReturnsSubClaim()
    {
        $payload = new Payload($this->jwtPayload);

        $this->assertEquals('some-user-uuid-string', $payload->getUserUUID());
    }

    public function testGetEmailReturnsEmailClaim()
    {
        $payload = new Payload($this->jwtPayload);

        $this->assertEquals('user@example.com', $payload->getEmail());
    }
}
