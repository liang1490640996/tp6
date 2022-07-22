<?php
// 应用公共文件

use think\facade\Config;
use Firebase\JWT\JWT;
use think\facade\Request;
use app\common\lib\IAes;
use app\common\lib\Redis;
use app\common\exception\ApiErrorException;

/**
 * 通用化API数据格式输出
 * User: lmg
 * Date: 2022/7/21 15:55
 * @param int $code
 * @param $message
 * @param array $data
 * @param int $httpCode
 * @param int $count
 * @return \think\response\Json
 */
function show($code = 200, $message, $data = [], $httpCode = 200, $count = 0)
{

    $data = [
        'code' => $code,
        'message' => $message,
        'data' => $data,
        'count' => $count,
    ];
    return json($data, $httpCode);
}

/**
 * 记录日志
 * @param string|array $data 请求数据 ['param1' => data1...]
 * @param string $filename 文件目录
 * @param int $clear 是否清空日志 0
 * 访问系统错误日志 https://api.vars3cf.com/static/logs/20210604/system_exception/log.txt
 * 访问日志 https://api.vars3cf.com/static/logs/20210604/test/15.txt
 */
function recordlog($data = 'message', $filename = 'log', $file_time = 'hour', $clear = FILE_APPEND)
{
    $time = time();
    $dir = "static/logs/" . date('Ymd') . '/' . $filename . "/"; #外层文件夹
    // 默认为小时份文件 ，否则保存为为天
    $hour = $file_time == 'hour' ? date('H', $time) . '.txt' : 'log.txt';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $data = is_array($data) ? $data : (array)$data;
    $str = '';
    foreach ($data as $key => $val) {
        $str .= "{$key}: " . print_r($val, true) . PHP_EOL;
    }
    $init = "=======================================================";
    file_put_contents($dir . $hour, date("Y-m-d H:i:s", $time) . PHP_EOL . $init . PHP_EOL . $str . PHP_EOL . PHP_EOL, $clear);
}

/**
 * 去除字符串2旁的空格
 * User: lmg
 * Date: 2022/7/21 15:46
 * @param array $data 接收的数组参数
 * @return array
 */
function trimString(array $data)
{
    if (is_array($data)) {
        foreach ($data as $k => $v) {
            if (is_string($v) || is_numeric($v)) {
                $data[$k] = trim($v);
            }
        }
    }
    return $data;
}

/**
 * curl请求
 * User: lmg
 * Date: 2022/7/21 16:51
 * @param string $url 请求地址 www.example.com
 * @param string $type 请求类型 GET|POST
 * @param array $data 请求参数 ['param1' => '', 'param2' => '']
 * @return array|mixed|string
 * @throws \GuzzleHttp\Exception\GuzzleException
 */
function guzzleHttp($url, $type = 'GET', $data = [])
{
    try {
        $client = new GuzzleHttp\Client(['base_uri' => $url, 'timeout' => 10.0]);
        $send_data = $type == 'GET' ? ['query' => $data] : ['form_params' => $data];
        $response = $client->request($type, $url, $send_data);
        $result = $response->getBody()->getContents();
        if (is_null(json_decode($result))) {
            return $result;
        }
        return \GuzzleHttp\json_decode($result, true);
    } catch (\Exception $e) {
        recordlog(['error' => $e->getMessage()], 'guzzle_http');
        return ['code' => 500, 'error' => $e->getMessage()];
    }
}

/**
 * JWT 生成验签 token
 * User: lmg
 * Date: 2022/7/21 16:56
 * @param array|string $data 用户的信息
 * @return string token
 */
function signToken($data)
{
    // 配置参数
    $config = Config::get('jwt');
    $algorithm = $config['algorithm'];// 加密算法
    $salt = $config['salt'];     // 盐
    $expired = $config['expired'];  // 过期时间
    $iat = $config['iat'];      // 签发时间
    $nbf = $config['nbf'];      // 生效时间

    // 构造 header
    $header = [
        'typ' => 'jwt',
        'alg' => $algorithm
    ];
    // 构造 payload
    $payload = [
        "iss" => $salt, //签发者 可以为空
        "aud" => '',    //面象的用户，可以为空
        "iat" => time() + $iat,      //签发时间
        "nbf" => time() + $nbf,    //在什么时候jwt开始生效（这里表示生成100秒后才生效）
        "exp" => time() + $expired,  //token 过期时间
        "data" => $data,      // 记录用户的信息如 user_id
    ];

    $jwt = JWT::encode($payload, $salt, $algorithm, $header);  //根据参数生成了 token
    return $jwt;
}

/**
 * JWT 验证 token
 * User: lmg
 * Date: 2022/7/21 16:57
 * @param string $token token
 * @return int[]
 */
function checkToken($token = '')
{
    $token = !empty($token) ? $token : Request::header('token');
    $config = Config::get('jwt');
    $salt = $config['salt'];
    $algorithm = $config['algorithm'];
    $result = ["code" => -1];
    try {
        $decoded = JWT::decode($token, $salt, [$algorithm]); //HS256方式，这里要和签发的时候对应
        $dataArr = (array)$decoded;
        $result['code'] = 0;
        $result['data'] = (array)$dataArr['data'];
        return $result;
    } catch (\Firebase\JWT\SignatureInvalidException $e) {
        $result['msg'] = "签名不正确";
        return $result;
    } catch (\Firebase\JWT\BeforeValidException $e) {
        $result['msg'] = "token失效";
        return $result;
    } catch (\Firebase\JWT\ExpiredException $e) {
        $result['msg'] = "token已过期";
        return $result;
    } catch (Exception $e) {
        $result['msg'] = empty($token) ? "token不能为空" : "未知错误";
        return $result;
    }
}

/**
 * 生成 key
 * User: lmg
 * Date: 2022/7/21 16:58
 * @param $id
 * @param $time
 * @return mixed
 */
function encryptKey($id, $time)
{
    $aesString = $id . '||' . $time;
    $key = IAes::encryptAes($aesString);
    return $key;
}

/**
 * 生成key ,token
 * User: lmg
 * Date: 2022/7/21 17:01
 * @param int $id 用户id
 * @param array $data 用户信息
 * @return string
 * @throws \Psr\SimpleCache\InvalidArgumentException
 */
function setTokenAndSign($id, $data)
{
    $data['openid'] = isset($data['openid']) ? $data['openid'] : 0;
    $data['at_time'] = time();
    $time = time();
    // 生成key
    $key = encryptKey($id, $time);
    $key = sha1($key . microtime(true));
    //生成缓存
    $redis = new Redis();
    $redis->hSet('jwt_login_token', $key, $data);

    # 生成token
    $ip = Request::ip();// 获取ip
    $domain = Request::domain();// 获取域名
    $data1 = [
        'key' => $key,
        'ip' => $ip,
        'domain' => $domain,
        'time' => $time
    ];
    $accessToken = signToken($data1);
    return $accessToken;
}

/**
 * 效验参数
 * User: lmg
 * Date: 2021/11/30 14:13
 * @param array $param 参数
 * @param string $scene 验证场景
 * @param string $className 类名
 * @throws ApiErrorException
 */
function paramValidate($param, $scene, $className)
{
    $validClass = "app\\common\\validate\\" . $className;
    $validate = new $validClass;
    if (!$validate->scene($scene)->check($param)) {
        throw new ApiErrorException($validate->getError());
    }
}
