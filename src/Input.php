<?php
namespace tc_lib;
/**
 * 描述：
 * Created at 2018/12/8 15:43 by 陈庙琴
 */
class Input
{
    const ALL       = 'REQUEST';
    const ONLY_GET  = 'GET';
    const ONLY_POST = 'POST';

    private static $instance = null;

    public static function instance($mode = self::ALL)
    {
        if (self::$instance === null) {
            self::$instance = new self($mode);
        }

        return self::$instance;
    }

    private function __construct($mode)
    {
        $this->data = ${'_' . $mode};
    }

    private $data = [];
    private $errMsg = [];

    // name=>require|string

    public function getInput($ruleGroups = [])
    {
        // 清空错误信息
        $this->errMsg = [];
        foreach ($ruleGroups as $key => $ruleGroup) {
            $rules = explode('|', $ruleGroup);
            foreach ($rules as $rule) {
                $result = $this->checkRule($rule, $key);
                if ($result === null) {
                    break;
                }
            }
        }
        if (!empty($this->errMsg)) {// 有错误信息，表示肯定有验证不通过的地方
            Output::instance()->error(Output::ERROR_UNKNOWN, implode(';', $this->errMsg))->send();
        }

        return $this->data;
    }

    private function checkRule($rawRule, $key)
    {
        $rawRule = explode(':', $rawRule);
        $rule    = $rawRule[0];

        switch ($rule) {
            case 'require':
                if (!isset($this->data[$key])) {
                    $this->errMsg[] = '[' . $key . '] is NOT FOUND';
                    return false;
                }
                break;
            case 'string':
                if (!is_string($key) || is_numeric($key)) {
                    $this->errMsg[] = '[' . $key . '] must be STRINGS';
                    return false;
                }
                break;
            case 'number':
                if (!is_numeric($key)) {
                    $this->errMsg[] = '[' . $key . '] must be a NUMBER';
                    return false;
                }
                break;
            case 'array':
                if (!is_array($key)) {
                    $this->errMsg[] = '[' . $key . '] must be a ARRAY';
                    return false;
                }
                break;
            case 'min':
                if (mb_strlen($key) < $rawRule[1]) {
                    $this->errMsg[] = 'the length of [' . $key . '] has SHORTER than the minimum length';
                    return false;
                }
                break;
            case 'max':
                if (mb_strlen($key) > $rawRule[1]) {
                    $this->errMsg[] = 'the length of [' . $key . '] has LONGER than the maximum length';
                    return false;
                }
                break;
            default:
                return false;
        }

        return true;
    }
}