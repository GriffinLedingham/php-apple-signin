<?php

namespace AppleSignIn;

use Exception;

/**
 * A class decorator for the Sign In with Apple payload produced by
 * decoding the signed JWT from a client.
 */
class ASPayload
{
    protected $_instance;

    public function __construct($instance)
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
        return (isset($this->_instance->$key)) ? $this->_instance->$key : null;
    }

    public function __set($key, $val)
    {
        return $this->_instance->$key = $val;
    }

    public function getEmail()
    {
        return (isset($this->_instance->email)) ? $this->_instance->email : null;
    }

    public function getUser()
    {
        return (isset($this->_instance->sub)) ? $this->_instance->sub : null;
    }

    public function verifyUser($user)
    {
        return $user === $this->getUser();
    }
}
