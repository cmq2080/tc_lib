<?php
/**
 * Created by PhpStorm.
 * User: mq
 * Date: 2019-06-28
 * Time: 10:49
 */

namespace tc_lib\func;


class Arr
{
    /**
     * 功能：查验数组中是否有为null/空字符串/0值的元素
     * Created By mq at 09:44 2019-06-28
     * @param $data
     * @param $keys
     * @param string $mode
     * @return bool
     */
    static public function check($data, $keys, $mode = 'nez')
    {
        $modes = str_split($mode);
        foreach ($keys as $key) {
            if (isset($data[$key]) === false) {
                return false;
            }

            if (in_array('n', $modes) === true && $data[$key] === null) {
                return false;
            }

            if (in_array('e', $modes) === true && $data[$key] === '') {
                return false;
            }

            if (in_array('z', $modes) === true && $data[$key] == 0) {
                return false;
            }

        }

        return true;
    }

    /**
     * 功能：删除数组中有特定值的元素
     * Created By mq at 下午6:35 2018/12/25
     * @param $array
     * @param $del_value
     * @param int $count 最多删除次数，0为无限制
     */
    static public function del(&$array, $del_value, $count = 0)
    {
        $i = 0;
        foreach ($array as $key => $value) {
            if ($value == $del_value) {
                unset($array[$key]);
                $i++;
                if ($count > 0 && $count === $i) {
                    break;
                }
            }
        }
    }

    /**
     * 功能：按白/黑名单过滤数组
     * 描述：白名单优先于黑名单
     * Created at 2018/10/1 15:17 by 陈庙琴
     * @param       $array
     * @param array $while_list
     * @param array $black_list
     * @return array
     */
    static public function select($array, $while_list = [], $black_list = [])
    {
        $result = [];
        if ($while_list) {
            foreach ($while_list as $key) {
                if (isset($array[$key])) {
                    $result[$key] = $array[$key];
                }
            }
        } else {
            $result = $array;
            foreach ($black_list as $key) {
                unset($result[$key]);
            }
        }
        return $result;
    }

    /**
     * 功能：去除数组中值为空字符串或NULL的元素（和array_filter唯一的区别就是保留0数字值）
     * Created at 2018/10/1 10:20 by 陈庙琴
     * @param $array
     * @return mixed
     */
    static public function trim($array)
    {
        foreach ($array as $key => $value) {
            if ($value === '' || $value === null) {
                unset($array[$key]);
            }
        }
        return $array;
    }

    /**
     * 功能：获取数组元素中的某一键所对应的值
     * 描述：可自动识别二维数组、对象数组以及它们json化后的格式
     * Created at 2019/3/24 14:52 by mq
     * @param $arrays
     * @param $key
     * @return array
     */
    static public function vls($arrays, $key)
    {
        if (!is_array($arrays)) {
            $arrays = json_decode($arrays);
        }
        $result = [];
        foreach ($arrays as $array) {
            if (isset($array[$key])) {
                $result[] = $array[$key];
            } else if (isset($array->$key)) {
                $result[] = $array->$key;
            }
        }
        return $result;
    }
}