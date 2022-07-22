<?php
namespace app\common\exception;

use app\common\lib\Dingding;
use think\exception\Handle;
use think\Response;
use Throwable;
use think\facade\Request;

class ApiHandleException extends Handle
{
	public $httpCode = 200;
    public $code = -1;
    public $data = [];
    public $count = 0;

    /**
     * @param \think\Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
    	// api接口错误返回
    	if ($e instanceof ApiErrorException) {
    	    $this->httpCode = $e->httpCode;
    	    $this->code = $e->code;
    	    $this->data = $e->data;
    	}

    	// api接口成功返回
    	if ($e instanceof ApiSuccessException) {
            $this->httpCode = $e->httpCode;
            $this->code = $e->code;
            $this->data = $e->data;
            $this->count = $e->count;
        }

        // 系统异常存入日志
        if($this->code == '-1' && !($e instanceof ApiErrorException) && !($e instanceof ApiSuccessException)) {

            // 错误的文件
            $file = $e->getFile();
            // 错误的行数
            $line = $e->getLine();
            // 错误码
            $code = $e->getCode();
            // 错误消息
            $msg  = $e->getMessage();

            // 请求 ip
            $request_ip = Request::ip();
            // 请求方式
            $request_method = Request::method();
            // 请求 url
            $request_url = Request::domain() . Request::url();
            // 请求用户信息
            // $memberInfo = checkLogin();
            $request_member = empty($memberInfo) ? '' : json_encode($memberInfo);
            // 请求 data
            $request_param = input('param.');
            $request_data  = PHP_EOL . '';
            if ($request_param) {
                foreach ($request_param as $key => $val)
                {
                    $request_data .= $key .' ：'. $val . PHP_EOL;
                }
            }

            // 写入日志
            $error = 'Request ip : '. $request_ip . PHP_EOL
                .'Request method : '. $request_method . PHP_EOL
                .'Request url : '. $request_url . PHP_EOL
                .'Request member : '. $request_member . PHP_EOL
                .'Request member token : '. Request::header('token') . PHP_EOL
                .'Request data : '. $request_data . PHP_EOL
                .'ErrorException in : ' . $file . ' line ' . $line . PHP_EOL
                .'ErrorMessage : '. $msg;

            // 钉钉接入
            $host = Request::host();
            if ($host == 'test.government.api.vars3cf.com' || $host == 'government.api.vars3cf.com') {

                // 生产环境下,不把系统错误消息展示给用户
                preg_match('/module not exists/', $e->getMessage(), $mt);
                preg_match('/controller not exists/', $e->getMessage(), $mt2);
                $SystemError = $mt2 ? 'error request!' : '服务器异常';

                // 如果是控制不存在或模块不存在，将不发送错误报告
                if (empty($mt) && empty($mt2)) {
                    /*$dingDing = new Dingding;
                    $dingDing->robotSend($error);*/
                }
            }

            recordlog($error, 'system_exception', 'day');
        }

        // 其他错误交给系统处理
        if(env('app_debug') == true) {
            return parent::render($request, $e);
        }

        // 错误消息
        $ErrorMsg = isset($SystemError) ? $SystemError : $e->getMessage();

        // 返回对应格式
        return show($this->code, $ErrorMsg, $this->data, $this->httpCode, $this->count);
    }

}