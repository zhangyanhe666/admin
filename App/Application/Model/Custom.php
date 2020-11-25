<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Model;
// use Application\Model\SysModel;
use Application\Tool\User;
class Custom extends Model{

    public $tableName   =   'sys.sys_custom';

    public $closeColumn     =   [];
    /**
     * 初始化被关闭字段
     * @Author   zhangyanhe
     * @DateTime 2020-10-14
     * @param    [type]     $uid     [description]
     * @param    [type]     $menu_id [description]
     * @return   [type]              [description]
     */
    public function initCloseColumn($uid,$menu_id){
        $item    =   $this->select(['uid'=>$uid,'menu_id'=>$menu_id])->current();
        if($item){
            $this->closeColumn  =   array_flip(explode(',', $item->shielded_column));
        }
        return $this;
    }


    // public function init() {
    //     $this->setAdapter('sys');
    //     $this->setTable('sys_custom');
    //     parent::init();
    // }
    // public function getMeans(){
    //     static $columnSwitch   =   array();
    //     if(empty($columnSwitch)){
    //         $userMean           =   $this->getUserMean();
    //         $columnSwitch       =   isset($userMean->shielded_column) && !empty($userMean->shielded_column) ? explode(',', $userMean->shielded_column) : array();
    //     }
    //     return !empty($columnSwitch) ? array_flip($columnSwitch) : array();
    // }
    // public function getUserMean(){
    //     return $this->where(array('menu_id'=>$this->getService('router')->getMenuId(),
    //                     'uid'=>User::userInfo()->id))->getRow();
    // }
    // public function editCustom($column,$val){
    //     $userMean       =   $this->getUserMean();
    //     $columnSwitch   =   array();
    //     if(isset($userMean->shielded_column) && !empty($userMean->shielded_column)){
    //         $columnSwitch   =   explode(',', $userMean->shielded_column);
    //         $columnSwitch   =   array_filter($columnSwitch, function($v) use($column){
    //             return $v !=$column;
    //         });
    //     }
    //     $val == 1 && $columnSwitch[] =   $column;
    //     $info                       =   array();
    //     $info['shielded_column']    =   !empty($columnSwitch) ? implode(',', $columnSwitch) : '';
    //     if($userMean->count()>0){
    //         return $this->update($info,array('id'=>$userMean->id));
    //     }else{      
    //         $info['uid']            =   User::userInfo()->id;
    //         $info['menu_id']        =   $this->getService('router')->getMenuId();
    //         return $this->insert($info);
    //     }
    // }
}
