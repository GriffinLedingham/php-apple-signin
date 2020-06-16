<?php

namespace AppleSignIn;

/**
 * A class decorator for the Sign In with Apple payload produced by
 * decoding the signed JWT from a client.
 */
class Payload
{
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

        $this->_instance = $instance;
    }

    public function getEmail(): ?string
    {
        return $this->_instance->email ?? null;
    }

    public function getUser(): ?string
    {
        return $this->_instance->sub ?? null;
    }

    public function getUserUUID(): ?string
    {
        return $this->_instance->sub ?? null;
    }

    public function verifyUser(string $user): bool
    {
        return $user === $this->getUser();
    }
}
