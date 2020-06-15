<?php

namespace AppleSignIn\Http;


/**
 * Class ASCurl
 * @package AppleSignIn
 * @author  Phil M
 * @since   1.0
 * @todo Implement http message interfaces for this instead of our own.
 */
interface Client
{
    public function get($url, $options = array(), &$errors = array());

    /**
     * @param $url
     * @param string $method 'post' or 'get'
     * @param null $postData
     * @param array $options
     * @param array $errors
     * @return mixed
     */
    public static function execute($url, $method, $postData = null, $options = array(), &$errors = array());

    public static function post($url, $postData, $options = array(), &$errors = array());
}