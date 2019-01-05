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
    // 日志前缀（日志存放的基本目录）
    const PREFIX = '/log/www/cj_tous_member';
    // 日志后缀
    const SUFFIX = '.log';

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

    // 实例化函数
    public static function dir($dirName)
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        self::$instance->setDir($dirName);
        self::$instance->header = null;
        return self::$instance;
    }

    private function __construct()
    {
    }

    // 设置总目录
    private function setDir($dirName)
    {
        $this->directory = self::PREFIX . '/' . $dirName . '/' . date('Ym');
    }

    public function header($header)
    {
        $this->header = $this->toString($header);
        return $this;
    }

    public function debug($content)// blue
    {
        $this->write($content, self::DEBUG);
    }

    public function info($content)// green
    {
        $this->write($content, self::INFO);
    }

    public function notice($content)// purple
    {
        $this->write($content, self::NOTICE);
    }

    public function warning($content)// orange
    {
        $this->write($content, self::WARNING);
    }

    public function error($content)// red
    {
        $this->write($content, self::ERROR);
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
        $content = $this->toString($content);

        // 开始写入
        try {
            if (is_dir($this->directory) === false) {
                mkdir($this->directory, 0755, true);
            }

            $fileName = date('d') . '-' . $level . self::SUFFIX;

            // 构建写入字符串
            $str = '<p>';
            $str .= '[' . date('Y-m-d H:i:s') . '] | <b style="' . $this->getStyle($level) . '">' . $this->header . "</b><br>\n";
            $str .= $content . "</p>\n";

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
        $dir      = self::PREFIX . '/' . $dirName . '/' . date('Ym', $time);
        $fileName = date('d', $time) . '-' . $level . self::SUFFIX;

        // 开始写入
        try {
            $str = file_get_contents($dir . '/' . $fileName);

            echo $str;
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    private function toString($content)
    {
        if (is_string($content) === false) {
            $content = json_encode($content, JSON_UNESCAPED_UNICODE);
        }

        return $content;
    }

    // 获取样式
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