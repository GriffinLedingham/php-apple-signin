<?php

namespace AppleSignInTest\Unit;

use AppleSignIn\Decoder;
use AppleSignIn\JWT;
use AppleSignIn\Payload;
use AppleSignIn\PublicKeyFetcher;
use PHPUnit\Framework\TestCase;

class DecoderTest extends TestCase
{

    /** @var PublicKeyFetcher */
    protected $fetcherMock;

    /** @var \stdClass */
    protected $jwtPayload;

    /**
     * @var \Prophecy\Prophecy\ObjectProphecy
     */
    private $jwtMock;

    public function setUp()
    {
        parent::setUp();

        $this->jwtPayload = new \stdClass();
        $this->jwtPayload->iss = 'https://appleid.apple.com';
        $this->jwtPayload->sub = 'some-user-uuid-string';
        $this->jwtPayload->email = 'user@example.com';
        $this->jwtPayload->aud = 'com.example.apple';

        $this->jwtMock = $this->prophesize(JWT::class);
        $this->fetcherMock = $this->prophesize(PublicKeyFetcher::class);
    }

    public function testGetAppleSignInPayloadReturnsPayloadFromJWT()
    {
        $this->jwtMock->getPublicKeyKid('header.claims.sig')->willReturn('a')->shouldBeCalled();
        $this->jwtMock->decodeJwt('header.claims.sig', '082d0ceb1beff92bb93a22aa138deadf', ['RS256'])->willReturn($this->jwtPayload)->shouldBeCalled();
        $this->fetcherMock->fetch('a')->willReturn(['alg' => 'RS256', 'publicKey' => '082d0ceb1beff92bb93a22aa138deadf'])->shouldBeCalled();

        $decoder = new Decoder($this->jwtMock->reveal(), $this->fetcherMock->reveal());

        $payload = $decoder->getAppleSignInPayload('header.claims.sig');

        $this->assertInstanceOf(Payload::class, $payload);
    }
}
