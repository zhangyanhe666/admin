<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller;
use Application\Base\PublicController;
use Library\Application\Common;
use Library\Application\Parameters;
class DispatchmapController extends PublicController{
    public $list;
    public $map;
    public $title;
    public $column;
    public function init() {
        parent::init();
        $mapObject    =   $this->tableConfig()->getCustomConfigParam();
        if($mapObject->count()>0){
            $this->title        =   $mapObject->title;
            $this->column       =   $mapObject->column;
            $this->map          =   new Parameters($mapObject->map);
            $this->list         =   new Parameters($mapObject->list);
        }else{
            $this->router()->toUrl(array('control'=>'tableConfig','action'=>'edit'),array('menuId'=>  $this->router()->getMenuId(),'msg'=>'displaymap配置有误请重新配置，或使用display调度器'));
        }
 
    }
    public function addAction() {
        parent::addAction();
        $this->viewData()->setVariable('checkBoxTitle',  $this->title);
        $this->viewData()->setVariable('checkBoxList',  $this->checkBoxList());
        $this->viewData()->addTpl('lib/checkBoxList');
    }
    public function editAction() {
        parent::editAction();
        $columnId   =   $this->viewData()->getVariable('item')->get($this->column);
        $where      =   array($this->map->selfId=>$columnId);
        $checkList  =   array_column($this->selfModel($this->map->table)->where($where)->getAll()->toArray(),$this->map->listId);
        $this->viewData()->setVariable('checkedList',  $checkList);
        $this->viewData()->setVariable('checkBoxTitle',  $this->title);
        $this->viewData()->setVariable('checkBoxList',  $this->checkBoxList());
        $this->viewData()->addTpl('lib/checkBoxList');
    }
    //添加操作
    public function doAddAction(){    
        parent::doAddAction();
        $this->addMap($this->selfTable()->getLastInsertValue());
        return $this->responseSuccess();
    }
    public function addMap($id){
        $listids        =   $this->getRequest()->getPost('__check');
        if(!empty($listids)){
            $selfId         =   array_fill(0,count($listids),  $id);
            $status         =   $this->selfModel($this->map->table)
                    ->batchInsert(array($this->map->selfId,$this->map->listId),array_map(null,$selfId,$listids));
        }
    }
    //编辑操作
    public function doEditAction() {
        parent::doEditAction();        
        $this->selfModel($this->map->table)->delete(array($this->map->selfId=>$this->getRequest()->getPost('id')));
        $this->addMap($this->getRequest()->getPost('id'));
        return $this->responseSuccess();
    }
    //获取列表数据
    protected function checkBoxList(){
        $listData       =   $this->selfModel($this->list->table)->where($this->list->where)->getAll()->toArray();
        $list           =   array_column($listData,$this->list->name,$this->list->id);
        return $list;
    }
}