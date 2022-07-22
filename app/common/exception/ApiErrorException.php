<?php

namespace app\common\exception;
use think\exception\ErrorException;

class ApiErrorException extends ErrorException {

    public $message = '';
    public $httpCode = 200;
    public $code = -1;
    public $data = [];

    /**
     * ApiErrorException constructor.
     * @param string $message
     * @param array $data
     * @param int $code
     * @param int $httpCode
     */
    public function __construct($message = '', $data = [], $code = -1, $httpCode = 200) {
        $this->httpCode = $httpCode;
        $this->message = $message;
        $this->code = $code;

        $this->data = $data;
    }
}