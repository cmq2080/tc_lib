<?php
/**
 * 描述：
 * Created at 2018/12/8 15:43 by 陈庙琴
 */

namespace tc_lib;

class Input
{
    const ALL       = 0;
    const ONLY_GET  = 1;
    const ONLY_POST = 2;

    private static $instance = null;

    private $data = [];
    private $errMsg = [];

    public static function instance($mode = self::ALL)
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        // 接收原始请求数据
        self::$instance->setData($mode);
        // 清空错误信息
        self::$instance->errMsg = [];

        return self::$instance;
    }

    private function __construct()
    {
    }

    /**
     * 功能：按请求方式接收原始请求数据
     * Created By mq at 11:42 2019-08-05
     * @param $mode
     */
    private function setData($mode)
    {
        // 超级变量还真不能用可变变量来获取
        switch ($mode) {
            case self::ALL:
                $this->data = $_REQUEST;
                break;
            case self::ONLY_GET:
                $this->data = $_GET;
                break;
            case self::ONLY_POST:
                $this->data = $_POST;
                break;
        }
    }

    /**
     * 功能：获取验证后的输入
     * Created By mq at 15:17 2019-08-06
     * @param array $ruleGroups
     * @return array|null
     */
    public function getInput($ruleGroups = [])
    {
        foreach ($ruleGroups as $key => $ruleGroup) {
            $rules = explode('|', $ruleGroup);
            foreach ($rules as $rule) {
                $result = $this->checkRule($rule, $key);
                if ($result === false) {
                    break;
                }
            }
        }
        // 验证失败，返回null
        if ($this->hasErr() === true) {
            return null;
        }

        return $this->data;
    }

    /**
     * 功能：验证输入规则
     * Created By mq at 11:42 2019-08-05
     * @param $rawRule
     * @param $key
     * @return bool
     */
    private function checkRule($rawRule, $key)
    {
        $rawRule = explode(':', $rawRule);
        $rule    = $rawRule[0];
        $value   = $this->data[$key];

        switch ($rule) {
            case 'require':
                if (isset($value) === false) {
                    $this->errMsg[] = '[' . $key . '] is NOT FOUND';
                    return false;
                }
                break;
            case 'string':
                if (is_string($value) === false || is_numeric($value)) {
                    $this->errMsg[] = '[' . $key . '] must be a STRING';
                    return false;
                }
                break;
            case 'number':
                if (is_numeric($value) === false) {
                    $this->errMsg[] = '[' . $key . '] must be a NUMBER';
                    return false;
                }
                break;
            case 'array':
                if (is_array($value) === false) {
                    $this->errMsg[] = '[' . $key . '] must be an ARRAY';
                    return false;
                }
                break;
            case 'min':
                if (mb_strlen($value) < $rawRule[1]) {
                    $this->errMsg[] = 'the length of [' . $key . '] has SHORTER than the minimum length';
                    return false;
                }
                break;
            case 'max':
                if (mb_strlen($value) > $rawRule[1]) {
                    $this->errMsg[] = 'the length of [' . $key . '] has LONGER than the maximum length';
                    return false;
                }
                break;
            default:
                return false;
        }

        return true;
    }

    /**
     * 功能：判断输入验证是否成功
     * Created By mq at 14:49 2019-08-06
     * @return bool
     */
    public function hasErr()
    {
        return empty($this->errMsg) === false;
    }

    /**
     * 功能：返回验证错误信息
     * Created By mq at 14:49 2019-08-06
     * @return array
     */
    public function errMsg()
    {
        return $this->errMsg;
    }
}