<?php

namespace AppleSignIn;

/**
 * A class decorator for the Sign In with Apple payload produced by
 * decoding the signed JWT from a client.
 */
class Payload
{
    const APPLE_TOKEN_ISSUER = 'https://appleid.apple.com';

    /** @var object|null  */
    protected $_instance;

    /**
     * Payload constructor.
     * @param object|null $instance
     * @throws Exception
     */
    public function __construct(?object $instance)
    {
        if (is_null($instance)) {
            throw new Exception('Payload received null JWT.');
        }

        if (!isset($instance->sub)) {
            throw new Exception('Payload received invalid JWT. Missing subject claim.');
        }

        if (!isset($instance->email)) {
            throw new Exception('Payload received invalid JWT. Missing email claim.');
        }

        if (!isset($instance->iss)) {
            throw new Exception('Payload received invalid JWT. Missing issuer claim.');
        }

        if ($instance->iss !== self::APPLE_TOKEN_ISSUER) {
            throw new Exception('Payload received invalid JWT. Invalid issuer claim. ' . $instance->iss);
        }

        $this->_instance = $instance;
    }

    public function getAudience(): string
    {
        return $this->_instance->aud ?? '';
    }

    public function getEmail(): string
    {
        return $this->_instance->email ?? '';
    }

    public function isEmailVerified(): bool
    {
        $verified = $this->_instance->email_verified ?? '';

        // Apple appears to (currently?) return this as a string with true in it.
        return $verified === 'true' || $verified === true;
    }

    public function getUser(): string
    {
        return $this->_instance->sub ?? '';
    }

    public function getUserUUID(): string
    {
        return $this->_instance->sub ?? '';
    }

    /**
     *
     *         if (!$payload->verifyAudience($audience)) {
     *          throw new \AppleSignIn\Exception('JWT issued to invalid audience');
     *         }
     * @param string $aud
     * @return bool
     */
    public function verifyAudience(string $aud): bool
    {
        return ($this->getAudience() === $aud);
    }

    public function verifyUser(string $user): bool
    {
        return $user === $this->getUser();
    }
}
