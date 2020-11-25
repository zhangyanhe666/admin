<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Controller;
use Application\Tool\Html;
use Application\Tool\User;
class MenuController extends Controller
{

    /***************
     * 对外Action
     ***************/
    //列表页
    /*************************************************
     *  title，menu，tool，list 
     *  执行时间3S（用户权限检测1S，菜单栏1S，数据获取1S）
     *************************************************/
    public function indexAction() {

        $list   =   $this->getService('menu')->getIndexList();

        $this->setVariable('options',['edit','copy','delete','transfer']);
        $this->setVariable('tools',['down','upload','add','tableconfig','custom','index']);
        $this->setVariable('list', $list);//获取分页列表数据
        return $this->responseList();
    }

    //获取当前使用的表对象
    public function selfTable(){
        $modelName  =   $this->getService('Menu')->menu->table_name;
        return $this->getService($modelName);
    }



    //添加页
    // public function addAction() {
    //     parent::addAction();
    //     $this->viewData()->setVariable('checkBoxTitle',  '数据库列表');
    //     $this->viewData()->setVariable('checkBoxList',  $this->tableList());
    //     $this->viewData()->addTpl('lib/checkBoxList');
    // }
    // //编辑页
    // public function editAction() {
    //     parent::editAction();
    //     $parent_id  =   $this->getRequest()->getQuery('id');
    //     $checkList  =   array_column($this->getService('Model\ChildMenu')->getMenuByParentId($parent_id),'table_name');
    //     $this->viewData()->setVariable('checkBoxTitle',  '数据库列表');
    //     $this->viewData()->setVariable('checkBoxList',  $this->tableList());
    //     $this->viewData()->setVariable('checkedList',  $checkList);
    //     $this->viewData()->addTpl('lib/checkBoxList');
    // }
    // //添加操作
    // public function doAddAction(){
    //     parent::doAddAction();
    //     $this->getService('Model\ChildMenu')->addChildMenu($this->selfTable()->getLastInsertValue(),$this->getRequest()->getPost('__check'));
    //     return $this->responseSuccess();
    // }
    // //编辑操作
    // public function doEditAction() {        
    //     parent::doEditAction();
    //     $id             =   $this->getRequest()->getPost('id');
    //     $check          =   $this->getRequest()->getPost('__check');
    //     $childMenu      =   array_column($this->getService('Model\ChildMenu')->where(array('parent_id'=>$id,'table_name != ""'))->getAll()->toArray(),'table_name','id');
    //     //获取项目列表
    //     $menuList       =   array_filter($childMenu,function($v){
    //         return strpos($v,'.');
    //     });
    //     $this->getService('Model\ChildMenu')->addChildMenu($id,array_diff($check,$menuList));
    //     $this->getService('Model\ChildMenu')->delChildMenu($id,array_diff($menuList,$check));
    //     return $this->responseSuccess();
    // }
    // public function tableList(){
    //     //获取所有数据库名
    //     $dbs            =   array_column($this->config()->dbConfig->toArray(),'key','key');
    //     $dbList         =   array_map(function($v){
    //         $information    =   $this->getService('Model\InformationSchema')->config($v);
    //         $tables     =   array_map(function($vv) use($v){
    //             $vv['val']   =   Common::mb_sub($vv['TABLE_COMMENT'],0,5)."({$vv['TABLE_NAME']})";
    //             $vv['key']   =   $v.'.'.$vv['TABLE_NAME'];
    //             return $vv;
    //         },$information->getAllTables());
    //         $table  =   array_column($tables,'val','key');
    //         return $table;
    //     },$dbs);
    //     return $dbList;
    // }
}
