<?php
/**
 * 描述：
 * Created at 2018/12/19 22:14 by 陈庙琴
 */

namespace tc_lib;

class Timer
{
    const LEVEL_SECOND      = 1;       // 单位：秒
    const LEVEL_MILLISECOND = 1000;    // 单位：毫秒
    const LEVEL_MICROSECOND = 1000000; // 单位：微秒

    private $start_at = null;
    private $end_at = null;

    public function __construct()
    {
        $this->start();
    }

    /**
     * 功能：计时开始
     * Created at 2018/12/19 22:26 by 陈庙琴
     */
    public function start()
    {
        $this->start_at = explode(' ', microtime());
    }

    /**
     * 功能：计时结束
     * Created at 2018/12/19 22:26 by 陈庙琴
     */
    public function end()
    {
        $this->end_at = explode(' ', microtime());
    }

    /**
     * 功能：计算时间
     * Created at 2020/2/1 14:26 by mq
     * @param int $level
     * @param bool $withUnit
     * @return float|int|string
     * @throws \Exception
     */
    public function count($level = self::LEVEL_SECOND, $withUnit = false)
    {
        $val0 = $this->end_at[0] * $level - $this->start_at[0] * $level;// 毫秒部分计算
        $val1 = ($this->end_at[1] - $this->start_at[1]) * $level;// 秒部分计算
        if ($withUnit === false) {
            return $val0 + $val1;
        }

        $unit = $this->getUnit($level);

        return ($val0 + $val1) . $unit;
    }

    /**
     * 功能：获取开始时间点
     * Created at 2018/12/19 22:27 by 陈庙琴
     * @return string
     */
    public function getStartAt()
    {
        return $this->start_at[1] . ltrim($this->start_at[0], '0');
    }

    /**
     * 功能：获取结束时间点
     * Created at 2018/12/19 22:27 by 陈庙琴
     * @return string
     */
    public function getEndAt()
    {
        return $this->end_at[1] . ltrim($this->end_at[0], '0');
    }

    /**
     * 功能：获取时间单位
     * Created at 2020/2/1 14:43 by mq
     * @param $level
     * @return mixed|string
     */
    private function getUnit($level)
    {
        $result = [
            self::LEVEL_SECOND      => 's',
            self::LEVEL_MILLISECOND => 'ms',
            self::LEVEL_MICROSECOND => 'μs',
        ];

        return isset($result[$level]) ? $result[$level] : '未知的时间单位';
    }
}