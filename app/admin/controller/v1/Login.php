<?php
/**
 * Created by PhpStorm.
 * User: lmg
 * Date: 2022/7/21 15:37
 */

namespace app\admin\controller\v1;


use app\admin\facade\LoginLogic;

class Login
{
    // 中间件
    protected $middleware = [
        'auth' => [
            'except' => ['login']
        ]
    ];

    /**
     * 登录首页
     */
    public function index()
    {
        LoginLogic::index();
    }

    /**
     * 登录
     */
    public function login()
    {
        LoginLogic::login();
    }

    /**
     * 退出登陆
     */
    public function loginOut()
    {
        LoginLogic::loginOut();
    }
}