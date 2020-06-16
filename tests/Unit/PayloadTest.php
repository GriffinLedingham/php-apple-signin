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
        $this->jwtPayload->iss = Payload::APPLE_TOKEN_ISSUER;
        $this->jwtPayload->sub = 'some-user-uuid-string';
        $this->jwtPayload->email = 'user@example.com';
        $this->jwtPayload->aud = 'com.example.apple';
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

    /**
     * @expectedException \AppleSignIn\Exception
     * @expectedExceptionMessage Payload received invalid JWT. Missing issuer claim.
     */
    public function testPayloadInstanciatedWithMissingIssuerClaim()
    {
        unset($this->jwtPayload->iss);

        $payload = new Payload($this->jwtPayload);
    }


    /**
     * @expectedException \AppleSignIn\Exception
     * @expectedExceptionMessage Payload received invalid JWT. Invalid issuer claim.
     */
    public function testPayloadInstanciatedWithInvalidIssuerClaim()
    {
        $this->jwtPayload->iss = 'invalid';

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

    public function testGetAudienceReturnsAudClaim()
    {

        $payload = new Payload($this->jwtPayload);

        $this->assertEquals('com.example.apple', $payload->getAudience());
    }

    public function testVerifyAudienceReturnsTrueWithCorrectAudience()
    {
        $payload = new Payload($this->jwtPayload);

        $this->assertTrue($payload->verifyAudience('com.example.apple'));
    }

    public function testVerifyAudienceReturnsFalseWithIncorrectAudience()
    {
        $payload = new Payload($this->jwtPayload);

        $this->assertFalse($payload->verifyAudience('invalid'));
    }
}
