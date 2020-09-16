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
    public static function get($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}
