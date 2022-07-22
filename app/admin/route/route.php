<?php

use think\facade\Route;

Route::group(function () {

    // 登录
    Route::post(':ver/login/index', ':ver.Login/index'); //登陆首页
    Route::post(':ver/login/login$', ':ver.Login/login'); //登录
    Route::post(':ver/login/login_out', ':ver.Login/loginOut'); //退出登陆

})->allowCrossDomain([
    'Access-Control-Allow-Origin' => '*',
    'Access-Control-Allow-Methods' => 'GET,POST,PUT,DELETE',
    'Access-Control-Allow-Credentials' => 'true',
    'Access-Control-Allow-Headers' => 'token,app-type,content-type,sign,X-Requested-With,X_Requested_With',
]);