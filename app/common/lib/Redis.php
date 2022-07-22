<?php
/**
 * Created by PhpStorm.
 * User: lmg
 * Date: 2022/7/21 17:10
 */

namespace app\common\lib;


use think\facade\Cache;

class Redis
{
    /**
     * redis句柄
     * @var redis
     */
    protected $redis;

    /**
     * 架构函数
     * @access public
     */
    public function __construct()
    {
        $this->redis = Cache::store('redis')->handler();
    }

    /**
     * 将一个或多个值插入到列表头部
     * @param string $key 键
     * @param string $value 值
     * @return int
     */
    public function lPush($key, $value)
    {
        return $this->redis->lPush($key, $value);
    }

    /**
     * 在列表中添加一个或多个值
     * @param string $key 键
     * @param string|array $value 值
     * @return int
     */
    public function rPush($key, $value)
    {
        return $this->redis->rPush($key, $value);
    }

    /**
     * 在队列列表中添加一个值
     * @param string $key 键
     * @param string|array $value 值
     * @param string|array $time 时间
     * @return int
     */
    public function queuePush($key, $value, $time = '')
    {
        $value['create_time'] = $time ? $time : time();
        return $this->redis->rPush($key, json_encode($value));
    }

    /**
     * 获取列表指定范围内的元素
     * @param string $key 键
     * @return array
     */
    public function lRange($key)
    {
        $len = $this->redis->lLen($key);
        return $this->redis->lRange($key, 0, $len);
    }

    /**
     * 获取队列列表
     * @param string $key 键
     * @return array 值
     */
    public function questionLrange($key)
    {
        $len = $this->redis->lLen($key);
        return $this->redis->lRange($key, 0, $len);
    }

    /**
     * 移出并获取列表的第一个元素
     * @param string $key 键
     * @return string 移出并获取列表的第一个元素
     */
    public function lPop($key)
    {
        return $this->redis->lPop($key);
    }

    /**
     * 移除列表的最后一个元素，返回值为移除的元素。
     * @param string $key 键
     * @return string 移出并获取列表的第一个元素
     */
    public function rPop($key)
    {
        return $this->redis->rPop($key);
    }

    /**
     * 获取列表长度
     * @param string $key 键
     * @return int
     */
    public function lLen($key)
    {
        return $this->redis->lLen($key);
    }

    /**
     * 移除列表元素
     * @param string $key 键
     * @param int $count 数量
     * @param string $value 匹配值
     * @return mixed
     */
    public function lRem($key, $count, $value)
    {
        return $this->redis->lRem($key, $count, $value);
    }

    /**
     * 获取存储在哈希表中指定字段的值。
     * @param string $key 键
     * @param string $field 字段
     * @return int
     */
    public function hGet($key, $field)
    {
        $result = $this->redis->hGet($key, $field);
        return json_decode($result, true);
    }

    /**
     * 将哈希表 key 中的字段 field 的值设为 value 。
     * @param string $key 键
     * @param string $field 字段
     * @param string|array $value 值
     * @return int
     */
    public function hSet($key, $field, $value)
    {
        return $this->redis->hSet($key, $field, json_encode($value));
    }

    /**
     * 获取在哈希表中指定 key 的所有字段和值
     * @param string $key 键
     * @return array 结果集
     */
    public function hGetall($key)
    {
        return $this->redis->hGetall($key);
    }

    /**
     * 删除一个或多个哈希表字段
     * @param string $key 键
     * @param string $field 字段
     * @return int 结果集
     */
    public function hDel($key, $field)
    {
        return $this->redis->hDel($key, $field);
    }

    /**
     * 设置指定 key 的值
     * @param string $key 键
     * @param string $value 值
     * @return int 结果集
     */
    public function set($key, $value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        return $this->redis->set($key, $value);
    }

    /**
     * 获取指定 key 的值。
     * @param string $key 键
     * @return string 结果集
     */
    public function get($key)
    {
        return $this->redis->get($key);
    }

    /**
     * 删除指定 key 的值。
     * @param string $key 键
     * @return int 结果集
     */
    public function del($key)
    {
        return $this->redis->del($key);
    }
}