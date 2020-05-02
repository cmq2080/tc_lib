<?php
/**
 * Created by PhpStorm.
 * User: mq
 * Date: 2019-07-26
 * Time: 09:48
 */

namespace tc_lib;


class Curl
{
    // 默认的User-Agent
    const USER_AGENT = 'tc_lib-Curl/3.x';

    const METHOD_GET    = 'GET';
    const METHOD_POST   = 'POST';
    const METHOD_PUT    = 'PUT';
    const METHOD_DELETE = 'DELETE';

    private static $instance = null;
    private $header = [];
    private $body = [];

    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        // 初始化header及body信息
        self::$instance->header = ['User-Agent:' . self::USER_AGENT];
        self::$instance->body   = [];

        return self::$instance;
    }

    private function __construct()
    {
    }

    /**
     * 功能：执行curl请求
     * Created By mq at 10:42 2019-07-26
     * @param $url
     * @param $method
     * @param array $data
     * @return bool|string
     * @throws \Exception
     */
    private function exec($url, $method)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0); // 头部不输出

        if ($method === self::METHOD_GET) { // GET方式提交，拼接URL字符串
            $url .= '?' . http_build_query($this->body);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else { // 非GET方式提交
            $this->header[] = 'X-HTTP-Method-Override:' . $method; // 设置提交方式
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); // 设置提交方式
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->body); // 设置提交的数据
        }

        // 装配header
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        // 执行
        $result = curl_exec($ch);
        if ($result === false) {
            throw new \Exception(curl_errno($ch));
        }
        curl_close($ch);

        return $result;
    }

    /**
     * 功能：设置头信息
     * Created By mq at 10:43 2019-07-26
     * @param $key
     * @param $value
     * @return string
     */
    public function header($key, $value)
    {
        return $this->header[] = $key . ':' . $value;
    }

    /**
     * 功能：设置多个头信息
     * Created By mq at 10:43 2019-07-26
     * @param $headers
     * @return $this
     */
    public function headers($headers)
    {
        foreach ($headers as $key => $value) {
            $this->header($key, $value);
        }

        return $this;
    }

    /**
     * 功能：__call函数反射
     * Created By mq at 15:06 2019-08-06
     * @param $name
     * @param $arguments
     * @return bool|string
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        if (in_array(strtoupper($name), [self::METHOD_GET, self::METHOD_POST, self::METHOD_PUT, self::METHOD_DELETE])) {
            $url    = $arguments[0];
            $method = strtoupper($name); // 这里是大写
            $data   = isset($arguments[1]) ? $arguments[1] : [];
            foreach ($data as $key => $value) { // 填充请求数据
                $this->body[$key] = $value;
            }

            return $this->exec($url, $method);
        }
    }
}