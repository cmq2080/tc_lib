<?php
/**
 * 描述：
 * Created at 2018/12/8 16:34 by 陈庙琴
 */

namespace tc_lib;


class Output
{
    // 错误状态码，可自定义
    const ERROR_UNKNOWN = 99999;


    private static $instance = null;

    /**
     * 功能：实例化函数
     * Created By mq at 11:48 2019-02-22
     * @return Output|null
     */
    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        // 清空数据部
        self::$instance->data = [];

        return self::$instance;
    }

    // 数据部
    private $data = [];

    private function __construct()
    {
    }

    /**
     * 功能：设置数据部
     * Created By mq at 11:51 2019-02-22
     * @param $data
     */
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

    /**
     * 功能：发送信息
     * Created By mq at 12:02 2019-02-22
     */
    public function send()
    {
        echo json_encode($this->data, JSON_UNESCAPED_UNICODE);
        die;
    }
}