<?php
/**
 * 描述：
 * Created at 2018/12/19 22:14 by 陈庙琴
 */

namespace tc_lib;

class Timer
{
    const LEVEL_SECOND=1;
    const LEVEL_MILLISECOND=2;
    const LEVEL_MICROSECOND=3;

    private $start_at =null;
    private $end_at   =null;

    public function __contruct()
    {
        $this->start();
    }

    /**
     * 功能：计时开始
     * Created at 2018/12/19 22:26 by 陈庙琴
     */
    public function start()
    {
        $this->start_at =explode(' ', microtime());
    }

    /**
     * 功能：计时结束
     * Created at 2018/12/19 22:26 by 陈庙琴
     */
    public function end()
    {
        $this->end_at =explode(' ', microtime());
    }

    /**
     * 功能：计算时间
     * Created at 2018/12/19 22:26 by 陈庙琴
     * @param int $level
     * @return float|int
     */
    public function count($level = self::LEVEL_SECOND)
    {
        switch ($level) {
            case self::LEVEL_SECOND:
                $y=1;
                break;
            case self::LEVEL_MILLISECOND:
                $y=1000;
                break;
            case self::LEVEL_MICROSECOND:
                $y=1000000;
                break;
        }

        $val0=$this->end_at[0]*$y-$this->start_at[0]*$y;
        $val1=$this->end_at[1]*$y-$this->start_at[1]*$y;
        return $val0+$val1;
    }

    /**
     * 功能：获取开始时间点
     * Created at 2018/12/19 22:27 by 陈庙琴
     * @return string
     */
    public function getStartAt()
    {
        return $this->start_at[0].' '.$this->start_at[1];
    }

    /**
     * 功能：获取结束时间点
     * Created at 2018/12/19 22:27 by 陈庙琴
     * @return string
     */
    public function getEndAt()
    {
        return $this->end_at[0].' '.$this->end_at[1];
    }
}