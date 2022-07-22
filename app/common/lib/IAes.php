<?php
/**
 * Created by PhpStorm.
 * User: lmg
 * Date: 2022/7/21 16:59
 */

namespace app\common\lib;


class IAes
{
    /**
     * Aes加密
     * @param $input
     * @param $key
     * @return string
     */
    public static function encryptAes($input, $sKey = '')
    {
        $sKey = $sKey ? $sKey : 'monkey_aes';
        $data = openssl_encrypt($input, 'AES-128-ECB', $sKey, OPENSSL_RAW_DATA);
        $data = base64_encode($data);
        return $data;
    }

    /**
     * Aes解密
     * @param $sStr
     * @param $sKey 加密秘钥
     * @return string
     */
    public static function decryptAes($sStr, $sKey = '')
    {
        $sKey = $sKey ? $sKey : 'monkey_aes';
        $decrypted = openssl_decrypt(base64_decode($sStr), 'AES-128-ECB', $sKey, OPENSSL_RAW_DATA);
        return $decrypted;
    }

    /**
     * 加密
     * @param $str
     * @return string
     */
    public static function signEncrypt($str)
    {
        $key = pack('H*', config('sign.admin_sign_key'));
        $iv = pack('H*', config('sign.admin_sign_halt'));
        $encrypted = openssl_encrypt($str, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($encrypted);;
    }

    /**
     * 解密
     * @param $str
     * @return string
     */
    public static function signDecrypt($str)
    {
        $key = pack('H*', config('sign.admin_sign_key'));
        $iv = pack('H*', config('sign.admin_sign_halt'));
        $deStr = base64_decode($str);
        $deStr = openssl_decrypt($deStr, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return trim($deStr);
    }
}