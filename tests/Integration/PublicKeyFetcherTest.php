<?php

namespace AppleSignInTest\Integration;

use AppleSignIn\Http\Curl;
use AppleSignIn\PublicKeyFetcher;
use PHPUnit\Framework\TestCase;

/**
 * Class PublicKeyFetcherTest
 * @package AppleSignInTest\Integration
 */
class PublicKeyFetcherTest extends TestCase
{
    /** @var string */
    protected const KID = '86D88Kf';

    /** @var PublicKeyFetcher */
    private $fetcher;

    public function setUp()
    {
        parent::setUp();

        $this->fetcher = new PublicKeyFetcher(new Curl());
    }

    public function testFetchRetrievesKeyWithWellKnownKid(): void
    {
        $result = $this->fetcher->fetch(self::KID);

        $this->assertArrayHasKey('publicKey', $result);
        $this->assertArrayHasKey('alg', $result);
    }

    /**
     * @expectedException \AppleSignIn\Exception
     */
    public function testFetchThrowsExceptionWithInvalidKid(): void
    {
        $this->fetcher->fetch('lol');
    }
}
