<?php
/**
 * Created by PhpStorm.
 * User: lmg
 * Date: 2022/7/21 15:39
 */

namespace app\admin\logic\v1;


use app\common\exception\ApiErrorException;
use app\common\exception\ApiSuccessException;
use app\common\lib\Redis;

/**
 * Class 登录
 * @package app\admin\logic\v1
 */
class Login extends Common
{
    /**
     * 登录首页
     * User: lmg
     * Date: 2022/7/21 17:21
     * @throws ApiErrorException
     * @throws ApiSuccessException
     */
    public function index()
    {
        try {
        } catch (ApiErrorException $e) {
            throw new ApiErrorException($e->getMessage());
        }
        throw new ApiSuccessException('已登陆,自动跳转到后台首页');
    }

    /**
     * 登录
     * User: lmg
     * Date: 2022/7/21 15:58
     * @throws ApiErrorException
     * @throws ApiSuccessException
     */
    public function login()
    {
        try {
            $token = setTokenAndSign(1, ['name' => 'test', 'age' => 18]);
        } catch (ApiErrorException $e) {
            throw new ApiErrorException($e->getMessage());
        }
        throw new ApiSuccessException('ok', $token);
    }

    /**
     * 退出登录
     * User: lmg
     * Date: 2022/7/21 17:44
     * @throws ApiErrorException
     * @throws ApiSuccessException
     */
    public function loginOut()
    {
        try {
            $data = checkToken();
            $key = isset($data['data']['key']) ? $data['data']['key'] : '';
            if (!$key) {
                throw new ApiErrorException('请先登录');
            }
            $redis = new Redis();
            $redis->hDel('jwt_login_token', $key);
        } catch (ApiErrorException $e) {
            throw new ApiErrorException($e->getMessage());
        }
        throw new ApiSuccessException('退出成功');
    }
}