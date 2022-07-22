<?php

namespace app\common\exception;
use think\Exception;

class ApiSuccessException extends Exception {

    public $message = '';
    public $httpCode = 200;
    public $code = 0;
    public $data = [];
    public $count = 0;

    /**
     * ApiSuccessException constructor.
     * @param string $message
     * @param array $data
     * @param int $code
     * @param int $httpCode
     * @param int $count
     */
    public function __construct($message = '', $data = [], $code = 0, $httpCode = 200, $count = 0) {
        $this->httpCode = $httpCode;
        $this->message = $message;
        $this->code = $code;
        $this->data = $data;
        $this->count = $count;
    }
}