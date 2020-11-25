<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Model;
use Library\Application\Config;
// use Application\Model\SysModel;
class CustomTableConfig extends Model{

    public $tableName   =   'sys.sys_custom_table_config';
    public $menuConfig;

    public $showColumns;

    /**
     * 设置表配置信息
     * @Author   zhangyanhe
     * @DateTime 2020-10-14
     * @param    [type]     $menu_id [description]
     */
    public function setMenuConfig(Custom $custom,$menu_id){
        $this->menuConfig   =   json_decode($this->getTableGateway()->select(['menu_id'=>$menu_id])->current()->config,true);
        $columns    =   array_filter($this->menuConfig['columnList'],function($v){
            return !in_array($v['viewType'], ['password','notUse','sign','bootstrap','sort']);
        });
        if(isset($this->menuConfig['linkColumns'])){
            $columns    =   Common::merge($columns, $this->menuConfig['linkColumns']);
        }
        $this->showColumns   =   array_diff_key($columns, $custom->closeColumn);
        return $this;
    }

    // public function init() {
    //     $this->setAdapter('sys');
    //     $this->setTable('sys_custom_table_config');
    //     parent::init();
    // }
    // public function editItem($id,$config,$tableName) {
    //     $w  =   empty($id) ? array('table_name'=>$tableName) : array('menu_id'=>$id);
    //     return $this->update(array('config'=>$config,'table_name'=>$tableName),$w);
    // }
    // public function addItem($id,$config,$tableName) {
    //     return $this->insert(array('config'=>$config,'table_name'=>$tableName,'menu_id'=>$id));
    // }
    // public function getConfig($id,$tableName){
    //     $w  =   empty($id) ? array('table_name'=>$tableName) : array('menu_id'=>$id);
    //     $config     =   json_decode($this->where($w)->getRow()->config,true);
    //     return new \Library\Application\Parameters($config);
    // }
}
