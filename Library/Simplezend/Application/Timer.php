<?php

/**
 * 计时器
 */

namespace Library\Application;

class Timer{
	
    public static $timeAnchor   =   array();
     //计时器
    public static function setTimeAnchor($k){
        self::$timeAnchor[$k]   = microtime(TRUE);
    }
    public static function getTimeAnchor(){
        $timeAnchor =   array();
        foreach (self::$timeAnchor as $k=>$v){
            $arr[]  =   $k;
            foreach (self::$timeAnchor as $kk=>$vv){
                !in_array($kk,$arr) && $timeAnchor[$k][$kk] =   round($vv-$v,3);
            }
        }
        return $timeAnchor;
    }
}