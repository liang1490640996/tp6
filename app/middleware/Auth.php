<?php
declare (strict_types=1);

namespace app\middleware;

use app\common\exception\ApiErrorException;
use app\common\lib\Redis;

class Auth
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        // 添加中间件执行代码
        try {
            // 验证token
            $token = checkToken();
            if ($token['code'] != 0) {
                throw new ApiErrorException($token['msg'], [], 401, 401);
            }

            $redis = new Redis();
            // 获取所有的登录缓存，过期的将删除(7天)
            $tokenAll = $redis->hGetall('jwt_login_token');
            if ($tokenAll) {
                foreach ($tokenAll as $k => $val) {
                    $val = json_decode($val, true);
                    $ttl = $val['at_time'] + 604800;
                    if ($ttl < time()) {
                        $redis->hDel('jwt_login_token', $k);
                    }
                }
            }

            // 获取用户信息
            $key = $token['data']['key'];
            $memberInfo = $redis->hGet('jwt_login_token', $key);
            if (empty($memberInfo)) {
                throw new ApiErrorException('请登录', [], 401, 401);
            }
            $request->memberInfo = $memberInfo;

        } catch (Exception $e) {
            throw new ApiErrorException($e->getMessage());
        }

        return $next($request);
    }
}
