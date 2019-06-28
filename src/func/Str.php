<?php
/**
 * Created by PhpStorm.
 * User: mq
 * Date: 2019-06-28
 * Time: 10:49
 */

namespace tc_lib\func;


class Str
{
    /**
     * 功能：字符串由gb2312转成utf-8
     * Created at 2018/10/1 14:54 by 陈庙琴
     * @param $string
     * @return string
     */
    static public function g2u($string)
    {
        return iconv('gb2312//IGNORE', 'utf-8', $string);
    }

    /**
     * 功能：字符串驼峰转蛇式
     * Created By mq at 14:28 2019-06-03
     * @param $string
     * @param string $prefix
     * @return string
     */
    static public function c2s($string, $prefix = '_')
    {
        $array = str_split($string);
        foreach ($array as $key => $value) {
            if ($key > 0 && ord($value) >= 65 && ord($value) <= 90) {
                $array[$key] = $prefix . chr(ord($value) + 32);
            }
        }

        return implode('', $array);
    }

    /**
     * 功能：字符串切片
     * Created By mq at 下午7:13 2018/12/25
     * @param $string
     * @param $length
     * @param string $sign
     * @return array
     */
    static public function exp($string, $length, $sign = '')
    {
        $strLength = mb_strlen($string, 'utf-8');
        for ($i = 0; $i < $strLength; $i += $length) {
            $len = $length;
            if ($i + $len > $strLength) {
                $len = $strLength - $i + 1;
            }
            $product_strs[] = $sign . mb_substr($string, $i, $len, 'utf-8') . $sign;
        }
        return $product_strs;
    }

    /**
     * 功能：字符串限制
     * Created at 2018/10/1 14:47 by 陈庙琴
     * @param        $string
     * @param        $length
     * @param string $sign
     * @return string
     */
    static public function limit($string, $length, $sign = '...')
    {
        if (mb_strlen($string) <= $length) {
            return $string;
        } else {
            $string = mb_substr($string, 0, $length) . $sign;
            return $string;
        }
    }

    /**
     * 功能：生成随机字符串
     * Created at 2018/10/1 14:47 by 陈庙琴
     * @param int $length
     * @param bool $only_number
     * @param bool $insensitive
     * @return string
     */
    static public function rand($length = 4, $only_number = false, $insensitive = false)
    {
        $number_text = '1234567890';
        $lower_text  = 'abcdefghijklmnopqrstuvwxyz';
        $upper_text  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ($only_number === true) {
            $text = $number_text;
        } else if ($insensitive === true) {
            $text = $number_text . $lower_text;
        } else {
            $text = $number_text . $lower_text . $upper_text;
        }
        $str   = '';
        $chars = str_split($text);// 做成随机字符数组可以提升性能，比以往的截取字符串可提升15-25%的性能（split函数已经废弃）
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[mt_rand(0, count($chars) - 1)];
        }
        return $str;
    }

    /**
     * 功能：字符串多路替换
     * Created at 2018/10/1 15:22 by 陈庙琴
     * @param $string
     * @param $replace_array
     * @return mixed
     */
    static public function rp($string, $replace_array)
    {
        foreach ($replace_array as $search => $replace) {
            $string = str_replace($search, $replace, $string);
        }
        return $string;
    }

    /**
     * 功能：字符串蛇式转驼峰
     * Created By mq at 14:28 2019-06-03
     * @param $string
     * @param string $prefix
     * @return string
     */
    static public function s2c($string, $prefix = '_')
    {
        $array = explode($prefix, $string);
        foreach ($array as $key => $value) {
            if ($key == 0) {
                continue;
            }

            $firstChar   = strtoupper(substr($value, 0, 1));
            $array[$key] = substr_replace($value, $firstChar, 0, 1);
        }

        return implode('', $array);
    }

    /**
     * 功能：字符串切片
     * Created By mq at 下午7:13 2018/12/25
     * @param $string
     * @param $length
     * @param string $sign
     * @return array
     */
    static public function slice($string, $length, $sign = '', $encoding = 'utf-8')
    {
        $str_length = mb_strlen($string, $encoding);
        $result     = [];
        for ($i = 0; $i < $str_length; $i += $length) {
            $len = $length;
            if ($i + $len > $str_length) {
                $len = $str_length - $i + 1;
            }
            $result[] = $sign . mb_substr($string, $i, $len, $encoding) . $sign;
        }
        return $result;
    }

    /**
     * 功能：字符串由utf-8转成gb2312
     * Created at 2018/10/1 14:54 by 陈庙琴
     * @param $string
     * @return string
     */
    static public function u2g($string)
    {
        return iconv('utf-8', 'gb2312//IGNORE', $string);
    }
}