<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Controller;
use Application\Base\Controller;
use Library\Application\Common;
use Application\Tool\Html;
class StructureController extends Controller{
    //初始化
    public function init() {
        //检测安装
        $this->checkInstall();
        //检测登陆
        $this->checkLogin();
        //检测权限
        $this->checkAuth();
        Html::setRouter($this->router());
    }
    //数据库列表页
    public function indexAction() {
        Html::addTool('add', '添加');
        Html::addOption('edit', '编辑',array(
            'href'=>  $this->router()->url(array('action'=>'edit'),array('id'=>'__key'))
        ));
        Html::addOption('tableList', '数据表管理',array(
            'href'=>  $this->router()->url(array('action'=>'tableList'),array('id'=>'__key'))
        ));
        Html::addOption('diffDb', '比对数据库',array(
            'href'=> "javascript:diffDb(this,'__key');"
        ));
        Html::addOption('tableword', '表文档',array(
            'href'=>  $this->router()->url(array('action'=>'tableword'),array('id'=>'__key')),
            'target'=>'_black'
        ));
        $this->viewData()->setVariable('items', $this->config()->dbConfig);
    }
    //编辑数据库信息页
    public function editAction(){
        
       
        $id         =   $this->getRequest()->getQuery('id');
        $this->viewData()->setVariable('id', $id);
        $this->viewData()->setVariable('item', $this->config()->dbConfig->$id);        
        return $this->template('structure/add');
    }
    //添加数据库信息页
    public function addAction(){
        
       
    }
    //添加数据库信息至配置
    public function doAddAction(){
        $id         =   $this->getRequest()->getPost('id');
        $dsn        =   $this->getRequest()->getPost('dsn');
        $username   =   $this->getRequest()->getPost('username');
        $password   =   $this->getRequest()->getPost('password');
        $this->getServer('Model\Structure')->addConfig($id,$dsn,$username,$password);
    }
    
    //数据库比对
    public function diffdbAction(){
        //需要将B中多出来的字段标记黄色
        
       
        $dbkeyA     =   $this->getRequest()->getQuery('dbkeyA');
        $dbkeyB     =   $this->getRequest()->getQuery('dbkeyB');
        $dbA        =   $this->getServer('Model\InformationSchema')->config($dbkeyA)->getColumnAll();
        $dbB        =   $this->getServer('Model\InformationSchema')->config($dbkeyB)->getColumnAll();
        $this->viewData()->setVariable('tablesA',Common::arrayCateKey(Common::arrayResetKey($dbA, 'name'),'tablename'));
        $this->viewData()->setVariable('tablesB',Common::arrayCateKey(Common::arrayResetKey($dbB, 'name'),'tablename'));
    }
    public function tablewordAction(){
        $id     =   $this->getRequest()->getQuery('id');
        $dbA    =   $this->getServer('Model\InformationSchema')->config($id)->getColumnAll();
        $dbA    =   Common::arrayCateKey($dbA,'tablename');
        $this->viewData()->setVariable('tablesA',$dbA);
    }
    //表列表
    public function tableListAction(){
        
             
        $this->viewData()->setVariable('list',$this->getServer('Model\InformationSchema')->config($this->getRequest()->getQuery('id'))->getAllTables('CREATE_TIME DESC'));
    }
    
}