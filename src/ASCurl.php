<?php
/**
 *   Author: Yanlongli <ahlyl94@gmail.com>
 *   Date:   2019/8/2
 *   IDE:    PhpStorm
 *   Desc:   CURL
 */

namespace AppleSignIn;

/**
 * Class ASCurl
 * @package AppleSignIn
 * @author  Zou Yiliang
 * @since   1.0
 */
class ASCurl
{
    public static function get($url, $options = array(), &$errors = array())
    {
        return self::execute($url, 'get', null, $options, $errors);
    }

    /**
     * @param $url
     * @param string $method 'post' or 'get'
     * @param null $postData
     * @param array $options
     * @param array $errors
     * @return mixed
     */
    public static function execute($url, $method, $postData = null, $options = array(), &$errors = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 150); //设置cURL允许执行的最长秒数

        //https 请求 不验证证书和host  生产环境还是验证一下，防止被恶意篡改
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        if ('post' === strtolower($method)) {
            curl_setopt($ch, CURLOPT_POST, true);
            if (null !== $postData) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            }
        }

        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }


        if (!($output = curl_exec($ch))) {
            $errors = array(
                    'errno' => curl_errno($ch),
                    'error' => curl_error($ch),
                ) + curl_getinfo($ch);
        }

        curl_close($ch);
        return $output;
    }

    public static function post($url, $postData, $options = array(), &$errors = array())
    {
        return self::execute($url, 'post', $postData, $options, $errors);
    }
}
