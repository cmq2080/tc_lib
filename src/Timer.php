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

    const UNIT_SECOND      = 's';
    const UNIT_MILLISECOND = 'ms';
    const UNIT_MICROSECOND = 'μs';

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
     * Created at 2018/12/19 22:26 by 陈庙琴
     * @param int $level
     * @return float|int
     */
    public function count($level = self::LEVEL_SECOND, $withUnit = false)
    {
        $val0 = $this->end_at[0] * $level - $this->start_at[0] * $level;// 毫秒部分计算
        $val1 = ($this->end_at[1] - $this->start_at[1]) * $level;// 秒部分计算
        if ($withUnit === false) {
            return $val0 + $val1;
        }

        switch ($level) {
            case self::LEVEL_SECOND:
                $unit = self::UNIT_SECOND;
                break;
            case self::LEVEL_MILLISECOND:
                $unit = self::UNIT_MILLISECOND;
                break;
            case self::LEVEL_MICROSECOND:
                $unit = self::UNIT_MICROSECOND;
                break;
            default:
                throw new \Exception('未知的时间等级');
        }
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
}