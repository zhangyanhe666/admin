<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Script\Model;
use Library\Db\Model;
class Push extends Model{
    
    public function getSubscribe(){
        $time       =   date('Y-m-d H:i:s');
        $starttime  =   date('H:i',time() + 600);
        $n          =   date('N');
        $nowtime    =   time();
        $where['tonight.starttime']     =   $starttime;
        $where['tonight.switch']        =   1;
        $where[]                =   "'{$time}' > tonight.online and '{$time}' < tonight.offline and find_in_set({$n},tonight.week) and ver >= 304";
        $items                  =   $this->getService('wukong.tonight')->join(array('b'=>'subscribe'),'tonight.id=b.show_id',array('user_id'))
                                    ->join(array('c'=>'wk_user'),'b.user_id=c.user_id',array('realId','dev'))
                                    ->where($where)->getAll()->toArray();
        return $items;
    }
    public function toPush($title,$realId,$desc,$playload,$dev){
        $this->getService('wukong.impush')->batchInsert1(array('title','realId','desc','playload','dev'),$title,$realId,$desc,$playload,$dev);
    }
    public function getPush(){
        return $this->getService('wukong.impush')->where(array('ispush'=>0))->getAll()->toArray();
    }
    public function updatePush($id,$result){
        return $this->getService('wukong.impush')->update(array('ispush'=>1,'result'=>$result),array('id'=>$id));
    }
    
    private function dateFormat($date){
        list($day)      =   explode(' ',$date);
        $sendday    =   strtotime(date('Y-m-d',strtotime($day)));//发布的那一天0点;
        $today0     =   strtotime(date('Y-m-d'));//今天0点
        if(($today0 - $sendday)>(2*24*3600)){
            return intval((time() - strtotime($date))/(24*3600))."天前";
        }elseif(($today0 - $sendday)>0){
            return "昨天";
        }elseif(time() - strtotime($date) > 3600){
            return intval((time() - strtotime($date))/3600)."小时前";
        }elseif(time() - strtotime($date) > 60){
            return intval((time() - strtotime($date))/60)."分钟前";
        }else{
            return "刚刚";
        }
    }
    
}