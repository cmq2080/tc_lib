<?php
/**
 * 描述：
 * Created at 2018/12/8 16:34 by 陈庙琴
 */

namespace tc_lib;


class Output
{
    const ERROR_UNKNOWN = 99999;


    private static $instance = null;

    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private $data = [];

    private function __construct()
    {
    }

    private function setData($data)
    {
        $this->data = $data;
    }

    public function success($data = [])
    {
        $this->setData($data);
        return $this;
    }

    public function error($errCode, $errMsg)
    {
        $this->setData(['err_code' => $errCode, 'err_msg' => $errMsg]);
        return $this;
    }

    public function send()
    {
        echo json_encode($this->data, JSON_UNESCAPED_UNICODE);
        die;
    }
}