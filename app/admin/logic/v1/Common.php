<?php
/**
 * Created by PhpStorm.
 * User: lmg
 * Date: 2022/7/21 15:40
 */

namespace app\admin\logic\v1;

use app\common\exception\ApiErrorException;
use app\common\validate\Page as PageValidate;
use think\App;
use think\exception\ValidateException;
use think\Request;

class Common
{
    /**
     * 当前的页
     * @var string
     */
    protected $page = '';

    /**
     * 每页显示多少条
     * @var string
     */
    protected $limit = '';

    /**
     * 查询条件的起始值
     * @var int
     */
    protected $from = 0;

    /**
     * 用户信息
     * @var array
     */
    protected $memberInfo = [];

    /**
     * @var \think\Request Request实例
     */
    protected $request;

    /**
     * 用户输入的参数
     * @param string $method 请求方式
     * @return mixed
     */
    public function getParam($method)
    {
        return $this->request->$method();
    }

    /**
     * 构造方法
     * Common constructor.
     * @param Request $request
     * @throws ApiErrorException
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        // 分页设置
        $this->pageSet();
        // 用户信息
        $this->memberInfo = $this->request->memberInfo;
    }

    /**
     * 获取分页
     */
    protected function pageSet()
    {
        try {
            // 效验数据
            $data = input('param.');
            $validate = new PageValidate();
            if (!$validate->scene('page')->check($data)) {
                throw new ApiErrorException($validate->getError());
            }
            $this->page = !empty($data['page']) ? $data['page'] : 1;
            $this->limit = !empty($data['limit']) ? $data['limit'] : 15;
            $this->from = ($this->page - 1) * $this->limit;
        } catch (ValidateException $e) {
            throw new ApiErrorException($e->getMessage());
        }
    }

    /**
     * 验证用户输入的参数
     * @param string $scene 验证场景
     * @param string $className 类名
     * @param string $method 请求方式
     * @return mixed
     * @throws ApiErrorException
     */
    public function paramValidate($scene, $className, $method = 'param')
    {
        $param = $this->getParam($method);
        $validClass = "app\\common\\validate\\" . $className;
        $validate = new $validClass;
        if (!$validate->scene($scene)->check($param)) {
            throw new ApiErrorException($validate->getError());
        }
        if (isset($param['ver'])) {
            unset($param['ver']);
        }
        return trimString($param);
    }
}