<?php

namespace app\common\validate;

use think\Validate;

class Page extends Validate
{
    protected $rule = [
        'page' => 'number',
        'limit' => 'number',
    ];

    protected $message = [
        'page.number' => '页数只能为数字',
        'limit.number' => '总条数只能为数字',
    ];

    protected $scene = [
        'page' => ['page', 'limit'],
    ];
}
