<?php
/**
 * Created by PhpStorm.
 * User: lmg
 * Date: 2022/7/21 15:37
 */

namespace app\admin\facade;


use think\Facade;

class LoginLogic extends Facade
{
    protected static function getFacadeClass()
    {
        return 'app\admin\logic\v1\Login';
    }
}