<?php
/**
 * Created by PhpStorm.
 * User: mq
 * Date: 2019/1/5
 * Time: 上午11:14
 */

namespace tc_lib;

class Log
{
    // 默认日志前缀（日志存放的基本目录）
    const PREFIX = '/var/log/tc_log';
    // 默认日志后缀
    const SUFFIX = '.log';

    // 日志前缀（日志存放的基本目录）
    public static $prefix = '';
    // 日志后缀
    public static $suffix = '';

    // 日志级别，由轻微到严重分别是debug（调试）、info（信息）、notice（留意）、warning（警告）、error（错误）
    const DEBUG   = 'debug';
    const INFO    = 'info';
    const NOTICE  = 'notice';
    const WARNING = 'warning';
    const ERROR   = 'error';

    private static $instance = null;
    // 总目录
    private $directory = null;
    // 头信息（标题）
    private $header = null;

    /**
     * 功能：实例化函数
     * Created By mq at 11:39 2019-02-22
     * @param $dirName
     * @param string $prefix
     * @param string $suffix
     * @return Log|null
     */
    public static function dir($dirName, $prefix = '', $suffix = '')
    {
        if (self::$instance === null) {
            self::$instance = new self($prefix, $suffix);
        }

        self::$instance->setDir($dirName);
        // 清空头信息
        self::$instance->header = null;
        return self::$instance;
    }

    private function __construct($prefix, $suffix)
    {
        // 优先选择传入的前、后缀
        self::$prefix = $prefix ? $prefix : self::PREFIX;
        self::$suffix = $suffix ? $suffix : self::SUFFIX;
    }

    // 设置总目录
    private function setDir($dirName)
    {
        $this->directory = self::$prefix . '/' . $dirName . '/' . date('Ym');
    }

    public function header($header)
    {
        $this->header = $this->toString($header);
        return $this;
    }

    /**
     * 功能：__call函数反射
     * Created By mq at 15:00 2019-03-08
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments)
    {
        if (in_array($name, [self::DEBUG, self::INFO, self::NOTICE, self::WARNING, self::ERROR])) {
            $content = $arguments[0];
            $this->write($content, $name);
        }
    }

    /**
     * 功能：写入日志（此类库的核心功能）
     * Created By mq at 下午2:34 2019/1/5
     * @param $content
     * @param $level
     */
    public function write($content, $level)
    {
        if (in_array($level, [self::DEBUG, self::INFO, self::NOTICE, self::WARNING, self::ERROR]) === false) {
            die('级别错误');
        }
        if ($this->header === null) {
            die('没有头信息');
        }
        $ip      = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
        $content = $this->toString($content);

        // 开始写入
        try {
            if (is_dir($this->directory) === false) {
                mkdir($this->directory, 0755, true);
            }

            $fileName = date('d') . '-' . $level . self::$suffix;

            // 构建写入字符串
            $str = '<div>';
            $str .= '[' . date('Y-m-d H:i:s') . '] | ' . $ip . ' | <b style="' . $this->getStyle($level) . '">' . $this->header . "</b><br>\n";
            $str .= $content . "</div>\n";

            error_log($str, 3, $this->directory . '/' . $fileName);
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * 功能：读取日志（此类库的次核心功能）
     * Created By mq at 下午2:44 2019/1/5
     * @param $dirName
     * @param $time
     * @param $level
     */
    public static function read($dirName, $time, $level)
    {
        $dir      = self::$prefix . '/' . $dirName . '/' . date('Ym', $time);
        $fileName = date('d', $time) . '-' . $level . self::$suffix;

        // 开始读取
        try {
            $str = 'No Content';
            if (is_file($dir . '/' . $fileName)) {
                $str = file_get_contents($dir . '/' . $fileName);
            }

            echo $str;
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * 功能：转换成字符串
     * Created By mq at 11:24 2019-02-22
     * @param $content
     * @return false|string
     */
    private function toString($content)
    {
        // 异常类自动转换文本
        if ($content instanceof \Exception) {
            $content = date('Y-m-d H:i:s') . ' - line ' . $content->getLine() . ' in ' . $content->getFile() . ':<span style="color:red;">' . $content->getMessage() . "</span><br>\n" . $content->getTraceAsString();
        }

        if (is_string($content) === false) {
            $content = json_encode($content, JSON_UNESCAPED_UNICODE);
        }

        return $content;
    }

    /**
     * 功能：获取样式
     * Created By mq at 11:25 2019-02-22
     * @param $level
     * @return string|null
     */
    private function getStyle($level)
    {
        $style = null;
        switch ($level) {
            case self::DEBUG:
                $style = 'color:blue';
                break;
            case self::INFO:
                $style = 'color:green';
                break;
            case self::NOTICE:
                $style = 'color:purple';
                break;
            case self::WARNING:
                $style = 'color:orange';
                break;
            case self::ERROR:
                $style = 'color:red';
                break;
        }
        return $style;
    }
}