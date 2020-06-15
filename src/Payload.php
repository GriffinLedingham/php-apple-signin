<?php

namespace AppleSignIn;

use Exception;

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
            throw new Exception('ASPayload received null instance.');
        }
        $this->_instance = $instance;
    }

    public function __call($method, $args)
    {
        return call_user_func_array(array($this->_instance, $method), $args);
    }

    public function __get($key)
    {
        return $this->_instance->$key ?? null;
    }

    public function __set($key, $val)
    {
        return $this->_instance->$key = $val;
    }

    public function __isset($name)
    {
        return isset($this->_instance->$name);
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
