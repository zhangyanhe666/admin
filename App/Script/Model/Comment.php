<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Script\Model;
use Application\Model\SysModel;
class Comment extends SysModel{
    
    public function init() {
        $this->setAdapter('wukong')->setTable('push_comment');
        parent::init();
    }
    
    public function commentList(){
        $data           =   array();
        $commentList    =   $this->columns(array('id','wkid','cid','touser_id','content','create_time'))
                            ->join(array('b'=>'wk_user_info'), 'push_comment.user_id=b.user_id', array('nickname','headimgurl'))
                            ->join(array('c'=>'wk_user'), 'push_comment.touser_id=c.id', array('realId','dev'))
                            ->where(array('push_comment.is_push'=>0,'wkid !=""'))->getAll()->toArray();
        $ids            =   array();    
        if(!empty($commentList)){
            foreach ($commentList as $v){
                $item           =   array();
                $payload        =   array();
                $ids[]          =   $v['id'];
                $payload['username']    =   $v['nickname'];
                $payload['headimgurl']  =   $v['headimgurl'];
                $payload['content']     =   $v['content'];
                $payload['create_time'] =   $this->dateFormat($v['create_time']);
                $payload['msgtype']     =   empty($v['content']) ? 'like' : 'comment';
                $payload['cid']         =   $v['cid'];
                $payload['wkid']        =   $v['wkid'];
                $payload['id']          =   $v['id'];
                $item['alias']          =   $v['realId'];
                $item['dev']            =   $v['dev'] == 'ios' ? 'ios' : 'android';
                $item['payload']        =   $payload;
                $data[]                 =   $item;
            }
            $this->update(array('is_push'=>1),array('id'=>$ids));
        }
        return $data;
    }
    public function subcribe(){
        $data   =   $this->getServer('wukong.push_comment')->join(array('c'=>'wk_user'), 'push_comment.touser_id=c.id', array('realId','dev'))->where(array('is_push'=>0,'wkid is null','(c.ver>=300 or c.ver=0)'))->getAll()->toArray();
        if(!empty($data)){
            $ids    =   array_column($data,'id');
            $this->getServer('wukong.push_comment')->update(array('is_push'=>1),array('id'=>$ids));
        }
        return $data;
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