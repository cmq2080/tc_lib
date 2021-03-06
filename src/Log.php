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
    public static $prefix = __DIR__ . '/../../../../runtime/tc_log';
    // 日志后缀
    public static $suffix = '.log';
    // 配置
    public static $config = [];

    // 日志级别，由轻微到严重分别是debug（调试）、info（信息）、notice（留意）、warning（警告）、error（错误）
    const LEVEL_DEBUG   = 'debug';
    const LEVEL_INFO    = 'info';
    const LEVEL_NOTICE  = 'notice';
    const LEVEL_WARNING = 'warning';
    const LEVEL_ERROR   = 'error';

    // 日志模式，分别是text、html、markdown
    const MODE_TEXT     = 1;
    const MODE_HTML     = 2;
    const MODE_MARKDOWN = 3;

    private static $instance = null;
    // 总目录
    private $directory = null;
    // 头信息（标题）
    private $header = null;
    // 日志模式，默认为text
    private $mode = self::MODE_TEXT;

    /**
     * 功能：实例化函数
     * Created By mq at 11:39 2019-02-22
     * @param $dirName
     * @param string $prefix
     * @param string $suffix
     * @return Log|null
     */
    public static function dir($dirName)
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        // 读取配置文件
        if (function_exists('config') === true) { // thinkPHP5.x、thinkPHP6.0、laravel4.x、laravel5.x、laravel6.0
            self::$config = config('log');
        } else if (function_exists('C') === true) { // thinkPHP 3.x
            self::$config = C('log');
        }

        // 设置前、后缀的最终量
        if (isset(self::$config['tc_log_path']) === true && self::$config['tc_log_path']) {
            self::$prefix = self::$config['tc_log_path'];
        }
        if (isset(self::$config['tc_log_suffix']) === true && self::$config['tc_log_suffix']) {
            self::$suffix = self::$config['tc_log_suffix'];
        }

        // 设置日志写入目录
        self::$instance->setDir($dirName);

        // 清空头信息
        self::$instance->header = null;

        return self::$instance;
    }

    private function __construct()
    {
    }

    /**
     * 功能：设置总目录
     * Created By mq at 11:03 2019-07-10
     * @param $dirName
     */
    private function setDir($dirName)
    {
        $this->directory = self::$prefix . '/' . $dirName . '/' . date('Ym');
    }

    /**
     * 功能：设置标题头
     * Created By mq at 11:03 2019-07-10
     * @param $header
     * @return $this
     */
    public function header($header)
    {
        $this->header = $this->toString($header);
        return $this;
    }

    /**
     * 功能：设置日志模式
     * Created at 2020/2/1 14:22 by mq
     * @param $mode
     * @return $this
     */
    public function mode($mode)
    {
        if (in_array($mode, [self::MODE_TEXT, self::MODE_HTML, self::MODE_MARKDOWN])) {
            $this->mode = $mode;
        }
        return $this;
    }

    /**
     * 功能：__call函数反射
     * Created at 2020/2/1 14:22 by mq
     * @param $name
     * @param $args
     * @throws \Exception
     */
    public function __call($name, $args)
    {
        if (in_array($name, [self::LEVEL_DEBUG, self::LEVEL_INFO, self::LEVEL_NOTICE, self::LEVEL_WARNING, self::LEVEL_ERROR])) {
            $content = $args[0];
            $this->write($content, $name);
        }
    }

    /**
     * 功能：写入日志（此类库的核心功能）
     * Created By mq at 下午2:34 2019/1/5
     * @param $content
     * @param $level
     * @throws \Exception
     */
    private function write($content, $level)
    {
        if (in_array($level, [self::LEVEL_DEBUG, self::LEVEL_INFO, self::LEVEL_NOTICE, self::LEVEL_WARNING, self::LEVEL_ERROR]) === false) {
            throw new \Exception('级别错误');
        }
        if ($this->header === null) {
            throw new \Exception('没有头信息');
        }
        $ip      = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
        $content = $this->toString($content);

        // 开始写入
        if (is_dir($this->directory) === false) {
            mkdir($this->directory, 0755, true);
        }

        // 寻找写入文件
        $fileName = date('d') . '-' . $level . self::$suffix; // 文件名：<日>-<级别>.<后缀>

        // 构建写入字符串
        $str = $this->mkContent($content, $level, $ip);

        error_log($str, 3, $this->directory . '/' . $fileName);
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
        $str = 'No Content';
        if (is_file($dir . '/' . $fileName)) {
            $str = file_get_contents($dir . '/' . $fileName);
        }

        echo $str;
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
        if (($content instanceof \Exception) === true) {
            $content = date('Y-m-d H:i:s') . ' - line ' . $content->getLine() . ' in ' . $content->getFile() . ':<span style="' . $this->getStyle(self::LEVEL_ERROR) . '">' . $content->getMessage() . "</span><br>\n" . $content->getTraceAsString();
        }

        // 不是字符串的直接JSON序列化
        if (is_string($content) === false) {
            $content = json_encode($content, JSON_UNESCAPED_UNICODE);
        }

        return $content;
    }

    /**
     * 功能：构建写入字符串
     * Created at 2020/2/1 14:23 by mq
     * @param $rawStr
     * @param $level
     * @param $ip
     * @return string
     * @throws \Exception
     */
    private function mkContent($rawStr, $level, $ip)
    {
        $str = '';
        switch ($this->mode) {
            case self::MODE_TEXT:
                $str .= '[' . date('Y-m-d H:i:s') . '] | ' . $ip . ' | ' . $this->header . "\r\n";
                $str .= $rawStr . "<br>\n";
                break;
            case self::MODE_HTML:
                $str .= '<div id="' . time() . '">';
                $str .= '[' . date('Y-m-d H:i:s') . '] | ' . $ip . ' | <b style="' . $this->getStyle($level) . '">' . $this->header . "</b><br>\n";
                $str .= $rawStr . "</div>\n";
                break;
            case self::MODE_MARKDOWN:
                $str .= '[' . date('Y-m-d H:i:s') . '] | ' . $ip . ' | <b style="' . $this->getStyle($level) . '">' . $this->header . "</b><br>\n";
                $str .= $rawStr . "\n" . '***' . "\n";
                break;
            default:
                throw new \Exception('未知的写入模式');
        }

        return $str;
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
            case self::LEVEL_DEBUG:
                $style = 'color:blue';
                break;
            case self::LEVEL_INFO:
                $style = 'color:green';
                break;
            case self::LEVEL_NOTICE:
                $style = 'color:purple';
                break;
            case self::LEVEL_WARNING:
                $style = 'color:orange';
                break;
            case self::LEVEL_ERROR:
                $style = 'color:red';
                break;
        }
        return $style;
    }
}