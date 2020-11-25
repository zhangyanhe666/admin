<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Script\Controller;
use Application\Base\Controller;

class ActivityController extends Controller{
    
    public function scriptStart(){
        $date   =   date('Y-m-d H:i:s');
        echo "脚本{$this->router()->getAction()}开始执行:{$date}\n";
        return microtime(TRUE);
    }
    public function scriptEnd($startTime){
        $endTime  =   microtime(TRUE);
        $allTime  =   $endTime-$startTime;
        echo "脚本{$this->router()->getAction()}执行结束\n执行总时长：{$allTime}s\n";
        exit;
    }
    public function onDispatch() {
        set_time_limit(0);
        $time   =   $this->scriptStart();
        parent::onDispatch();
        $this->scriptEnd($time);
    }
    public function updateUserAction(){
        //获取所有在线活动        
        $w['switch']    =   0;
        $w[]            =   'start_time < now() and end_time > now()';
        $activitys      =   $this->getService('wukong.activity')->where($w)->getAll();
        if(!empty($activitys)){
            foreach ($activitys as $v){
                $this->getService('wukong.activity_user')->update(array('shared'=>0,'isVarifi'=>0,'extract_num'=>$v['extract_num']),array('act_id'=>$v['id']));
            }
        }
        echo '脚本共执行（'.count($activitys).'）条数据';
    }
    public function testupdateUserAction(){
        //获取所有在线活动        
        $w['switch']    =   0;
        $w[]            =   'start_time < now() and end_time > now()';
        $activitys      =   $this->getService('wukong214.activity')->where($w)->getAll();
        if(!empty($activitys)){
            foreach ($activitys as $v){
                $this->getService('wukong214.activity_user')->update(array('shared'=>0,'isVarifi'=>0,'extract_num'=>$v['extract_num']),array('act_id'=>$v['id']));
            }
        }
        echo '脚本共执行（'.count($activitys).'）条数据';
    }
}