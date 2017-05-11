<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Script\Controller;
use Application\Base\Controller;

class BaseController extends Controller{
    
    public function execScript($callback,$scriptName='script'){
        set_time_limit(0);
        $date       =   date('Y-m-d H:i:s');
        if(is_array($callback)){
            $scriptName =   $callback[1];
        }
        $msg[]      =   "脚本{$scriptName}开始执行:{$date}";
        $startTime  =   microtime(TRUE);
        $msg[]      =   call_user_func($callback);
        $endTime    =   microtime(TRUE);
        $allTime    =   $endTime-$startTime;
        $msg[]      =   "脚本{$scriptName}执行结束";
        $msg[]      =   "执行总时长：{$allTime}s";
        return $msg;
    }
}