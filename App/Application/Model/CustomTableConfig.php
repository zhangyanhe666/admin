<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Model;
use Application\Model\SysModel;
class CustomTableConfig extends SysModel{
    public function init() {
        $this->setAdapter('sys');
        $this->setTable('sys_custom_table_config');
        parent::init();
    }
    public function editItem($id,$config,$tableName) {
        $w  =   empty($id) ? array('table_name'=>$tableName) : array('menu_id'=>$id);
        return $this->update(array('config'=>$config,'table_name'=>$tableName),$w);
    }
    public function addItem($id,$config,$tableName) {
        return $this->insert(array('config'=>$config,'table_name'=>$tableName,'menu_id'=>$id));
    }
    public function getConfig($id,$tableName){
        $w  =   empty($id) ? array('table_name'=>$tableName) : array('menu_id'=>$id);
        $config     =   json_decode($this->where($w)->getRow()->config,true);
        return new \Library\Application\Parameters($config);
    }
}
