<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Model;
use Application\Model\SysModel;
class AdminGroup extends SysModel{
    
   
    public function init() {
        $this->setAdapter('sys')->setTable('sys_user_group');
        parent::init();
    }
    public function addAuthMap($id,$map){
        if(!empty($map)){
            $column =   array('gid','menu_id','authority');
            $gid        =   array_fill(0,count($map),$id);
            $menu_id    =   array_keys($map);
            $authority  =   array_values($map);
            $status     =   $this->getServer('sys.sys_group_map')->batchInsert($column,array_map(null,$gid,$menu_id,$authority));
            return $status;
        }
        return false;
    }
    public function delAuthMap($id){
        $status     =   $this->getServer('sys.sys_group_map')->delete(array('gid'=>$id));
        return $status;
    }
    public function getGroupAuth($gid){
        return $this->getServer('sys.sys_group_map')->where(array('gid'=>$gid))->getAll()->toArray();
    }
    //判断超级管理员
    public function isSuperAdmin($gid){
        $hasAuth    =   $this->getServer('sys.sys_group_map')->where(array('gid'=>$gid))->count();
        return !$hasAuth;
    }
    
}
