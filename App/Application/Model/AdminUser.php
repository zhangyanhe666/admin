<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Model;
use Application\Model\SysModel;
use Library\Application\Common;
use Library\Db\Sql\Predicate\Expression;
use Application\Tool\User;
use Library\Application\Parameters;
use Application\Tool\Authority;
class AdminUser extends SysModel{
    public $userAuth    =   array();

    public function init() {
        $this->setAdapter('sys')->setTable('sys_user');
        parent::init();
    }

    //检测用户登录信息
    public function login($userName,$passWd){
        $userInfo   =   $this->where(array('username'=>$userName,'password'=>$passWd))->getRow();
        if($userInfo->count()>0){
            $this->update(array('login_num'=>new \Library\Db\Sql\Predicate\Expression('login_num+1')),array('id'=>$userInfo->id));
            return $userInfo;
        }else{
            return FALSE;
        }
    }
    public function addAuthMap($id,$map){
        if(!empty($map)){
            $column =   array('uid','menu_id','authority');
            $gid        =   array_fill(0,count($map),$id);
            $menu_id    =   array_keys($map);
            $authority  =   array_values($map);
            $status     =   $this->getService('sys.sys_user_map')->batchInsert($column,array_map(null,$gid,$menu_id,$authority));
            return $status;
        }
        return false;
    }
    public function delAuthMap($id){
        $status     =   $this->getService('sys.sys_user_map')->delete(array('uid'=>$id));
        return $status;
    }
    
    //检测用户权限
    public function auth($menuId){
        if($this->getService('Model\AdminGroup')->isSuperAdmin(User::userInfo()->gid)){
            User::$isSuperAdmin =   TRUE;
            return TRUE; 
        }
        //判断非权限管制或超级管理员组
        if(empty($menuId)){
            return true;
        }
        if($this->getService('Model\ChildMenu')->getItem($menuId)->parent_id == 0){
            return true;
        }
        $action             =   $this->getService('router')->getAction();
        $actionAuth         =   Authority::actionAuth($action);
        $gwhere             =   
        $uwhere             =   array(
            'menu_id'   =>  $menuId,
            "(authority & {$actionAuth}) = {$actionAuth}"
        );
        $gwhere['gid']      =   User::userInfo()->gid;
        $gstatus            =   $this->getService('sys.sys_group_map')->where($gwhere)->count();
        //为用户设定权限
        /*$uwhere['uid']      =   User::userInfo()->id;
        $ustatus            =   $this->getService('sys.sys_user_map')->where($uwhere)->count();*/
        return $gstatus;
    }
    public function getUserAuth($uid){
        return $this->getService('sys.sys_user_map')->where(array('uid'=>$uid))->getAll()->toArray();
    }
}
