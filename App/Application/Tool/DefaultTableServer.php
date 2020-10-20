<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Tool;
use Library\Application\Common;
class DefaultTableServer extends Tool{
    public $menuId;
    public function init(){
        $this->menuId   =   $this->getService('router')->getMenuId();
    }
    public function setMenuId($menuId){
        $this->menuId   =   $menuId;
        return $this;
    }
    public function db(){
        $menu           =   $this->getService('Model\ChildMenu')->getItem($this->menuId);
        if($menu->count()){
            $table    =   $menu->table_name;
        }  else {
            throw new \Exception('菜单配置错误');
        }        
        return $this->getService($table);
    }
}