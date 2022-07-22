<?php

// +----------------------------------------------------------------------
// | JWT设置
// +----------------------------------------------------------------------
return [
    // algorithm 算法
    'algorithm' => 'HS256',
    // salt 加密盐
    'salt'      => '8snD5WOHwzAwPdI9QaoHZ50eHxrEkl2e',
    // 过期时间(秒)
    'expired'   => 604800,
    // iat 签发时间(秒)
    'iat'       => 0,
    // jwt开始生效时间(秒)
    'nbf'       => 0,
];